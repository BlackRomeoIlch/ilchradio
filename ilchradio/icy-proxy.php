<?php
/**
 * ICY Metadata Proxy
 * Reads ICY (Icecast/Shoutcast) stream metadata and returns JSON.
 * Follows HTTP 301/302 redirects automatically.
 *
 * Usage: icy-proxy.php?url=https://your-stream-url/stream.mp3
 *
 * @copyright Ilch Radio Layout
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$streamUrl = isset($_GET['url']) ? trim($_GET['url']) : '';

if (empty($streamUrl)) {
    echo json_encode(['error' => 'No URL provided', 'title' => '', 'station' => '']);
    exit;
}

if (!filter_var($streamUrl, FILTER_VALIDATE_URL)) {
    echo json_encode(['error' => 'Invalid URL', 'title' => '', 'station' => '']);
    exit;
}

$scheme = strtolower(parse_url($streamUrl, PHP_URL_SCHEME));
if (!in_array($scheme, ['http', 'https'], true)) {
    echo json_encode(['error' => 'Invalid URL scheme', 'title' => '', 'station' => '']);
    exit;
}

// Cache 15 Sekunden
$cacheFile = sys_get_temp_dir() . '/ilchradio_icy_' . md5($streamUrl) . '.json';
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 15) {
    echo file_get_contents($cacheFile);
    exit;
}

$data   = readIcyData($streamUrl, 5); // bis zu 5 Redirects folgen
$result = json_encode([
    'title'   => $data['title']   ?? '',
    'station' => $data['station'] ?? '',
]);

file_put_contents($cacheFile, $result);
echo $result;

// ─────────────────────────────────────────────────────────────
// ICY-Daten lesen – folgt Redirects
// ─────────────────────────────────────────────────────────────
function readIcyData(string $url, int $maxRedirects = 5): array
{
    for ($i = 0; $i <= $maxRedirects; $i++) {

        $parsed  = parse_url($url);
        $host    = $parsed['host'] ?? '';
        $port    = $parsed['port'] ?? ($parsed['scheme'] === 'https' ? 443 : 80);
        $path    = ($parsed['path'] ?? '/') . (!empty($parsed['query']) ? '?' . $parsed['query'] : '');
        $ssl     = ($parsed['scheme'] === 'https') ? 'ssl://' : '';
        $timeout = 8;

        $fp = @stream_socket_client(
            $ssl . $host . ':' . $port,
            $errno, $errstr, $timeout,
            STREAM_CLIENT_CONNECT,
            stream_context_create(['ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ]])
        );

        if (!$fp) {
            return ['title' => '', 'station' => ''];
        }

        stream_set_timeout($fp, $timeout);

        $request  = "GET {$path} HTTP/1.0\r\n";
        $request .= "Host: {$host}\r\n";
        $request .= "User-Agent: Mozilla/5.0 IlchRadio/1.0\r\n";
        $request .= "Icy-MetaData: 1\r\n";
        $request .= "Connection: close\r\n\r\n";
        fwrite($fp, $request);

        // Header lesen
        $statusLine  = '';
        $metaInt     = 0;
        $stationName = '';
        $location    = '';
        $firstLine   = true;
        $isRedirect  = false;

        while (!feof($fp)) {
            $line = fgets($fp, 4096);
            if ($line === false) break;

            $trimmed = rtrim($line);

            if ($firstLine) {
                $statusLine = $trimmed;
                $firstLine  = false;
                // 301 / 302 / 303 / 307 / 308 → Redirect
                if (preg_match('#HTTP/\S+\s+(30[1-8])#i', $trimmed)) {
                    $isRedirect = true;
                }
                continue;
            }

            if ($trimmed === '') break; // Ende Header

            if (stripos($trimmed, 'location:') === 0) {
                $location = trim(substr($trimmed, 9));
            }
            if (stripos($trimmed, 'icy-metaint:') === 0) {
                $metaInt = (int)trim(substr($trimmed, 12));
            }
            if (stripos($trimmed, 'icy-name:') === 0) {
                $stationName = trim(substr($trimmed, 9));
            }
        }

        // Redirect folgen
        if ($isRedirect && !empty($location)) {
            fclose($fp);
            // Relative Redirects auflösen
            if (strpos($location, 'http') !== 0) {
                $base    = $parsed['scheme'] . '://' . $parsed['host'];
                $location = $base . '/' . ltrim($location, '/');
            }
            $url = $location;
            continue;
        }

        // Kein ICY-Metablock vorhanden
        if ($metaInt <= 0) {
            fclose($fp);
            return ['title' => '', 'station' => $stationName];
        }

        // Audio-Bytes überspringen
        $skipped = 0;
        while ($skipped < $metaInt && !feof($fp)) {
            $chunk = fread($fp, min(4096, $metaInt - $skipped));
            if ($chunk === false || $chunk === '') break;
            $skipped += strlen($chunk);
        }

        // Metadaten-Längen-Byte lesen
        $lenByte = fread($fp, 1);
        if ($lenByte === false || $lenByte === '') {
            fclose($fp);
            return ['title' => '', 'station' => $stationName];
        }

        $metaLen = ord($lenByte) * 16;
        if ($metaLen === 0) {
            fclose($fp);
            return ['title' => '', 'station' => $stationName];
        }

        // Metadaten-Block lesen
        $metaData  = '';
        $remaining = $metaLen;
        while ($remaining > 0 && !feof($fp)) {
            $chunk = fread($fp, $remaining);
            if ($chunk === false || $chunk === '') break;
            $metaData  .= $chunk;
            $remaining -= strlen($chunk);
        }

        fclose($fp);

        $title = '';
        if (preg_match("/StreamTitle='([^;]*)'/", $metaData, $m)) {
            $title = trim(rtrim($m[1], "\x00"));
        }

        return ['title' => $title, 'station' => $stationName];
    }

    return ['title' => '', 'station' => ''];
}
