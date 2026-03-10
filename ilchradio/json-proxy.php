<?php
/**
 * JSON API Proxy
 * Fetches an external JSON URL server-side (bypasses browser CORS).
 *
 * Usage:       json-proxy.php?url=https://example.com/playlist.json
 * Debug-Modus: json-proxy.php?url=...&debug=1  → zeigt rohe Antwort
 *
 * @copyright Ilch Radio Layout
 */

$debug = isset($_GET['debug']) && $_GET['debug'] === '1';

if (!$debug) {
    header('Content-Type: application/json; charset=utf-8');
}
header('Access-Control-Allow-Origin: *');

$url = isset($_GET['url']) ? trim($_GET['url']) : '';

if (empty($url)) {
    echo json_encode(['error' => 'No URL provided']);
    exit;
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo json_encode(['error' => 'Invalid URL']);
    exit;
}

$scheme = strtolower(parse_url($url, PHP_URL_SCHEME));
if (!in_array($scheme, ['http', 'https'], true)) {
    echo json_encode(['error' => 'Invalid URL scheme']);
    exit;
}

// Cache überspringen im Debug-Modus
$cacheFile = sys_get_temp_dir() . '/ilchradio_json_' . md5($url) . '.json';
if (!$debug && file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 20) {
    echo file_get_contents($cacheFile);
    exit;
}

// Realistischere Browser-Header damit Server nicht blockiert
$headers  = "Accept: application/json, text/javascript, */*; q=0.01\r\n";
$headers .= "Accept-Language: de-DE,de;q=0.9,en;q=0.8\r\n";
$headers .= "Referer: " . parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . "/\r\n";
$headers .= "X-Requested-With: XMLHttpRequest\r\n";
$headers .= "Connection: close\r\n";

$ctx = stream_context_create([
    'http' => [
        'method'          => 'GET',
        'timeout'         => 10,
        'ignore_errors'   => true,
        'follow_location' => true,
        'max_redirects'   => 5,
        'user_agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'header'          => $headers,
    ],
    'ssl' => [
        'verify_peer'      => false,
        'verify_peer_name' => false,
    ],
]);

$body = @file_get_contents($url, false, $ctx);

if ($debug) {
    header('Content-Type: text/plain; charset=utf-8');
    // Antwort-Header anzeigen
    if (isset($http_response_header)) {
        echo "=== HTTP HEADERS ===\n";
        echo implode("\n", $http_response_header) . "\n\n";
    }
    echo "=== BODY (erste 2000 Zeichen) ===\n";
    echo substr($body ?: '[leer]', 0, 2000);
    exit;
}

if ($body === false || $body === '') {
    $err = json_encode(['error' => 'Could not fetch URL']);
    file_put_contents($cacheFile, $err);
    echo $err;
    exit;
}

// Prüfen ob gültiges JSON
$decoded = json_decode($body, true);
if ($decoded === null) {
    $err = json_encode(['error' => 'Invalid JSON response', 'raw' => substr($body, 0, 200)]);
    file_put_contents($cacheFile, $err);
    echo $err;
    exit;
}

file_put_contents($cacheFile, $body);
echo $body;
