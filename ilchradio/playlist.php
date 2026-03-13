<?php
/**
 * Playlist Generator – erzeugt Playlist-Dateien für externe Mediaplayer.
 *
 * Usage: playlist.php?format=m3u|pls|asx|ram&url=https://...&name=Station
 *
 * @copyright Ilch Radio Layout
 */

$format = isset($_GET['format']) ? strtolower(trim($_GET['format'])) : 'm3u';
$url    = isset($_GET['url'])    ? trim($_GET['url'])    : '';
$name   = isset($_GET['name'])   ? trim($_GET['name'])   : 'Radio';

if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    exit;
}

$scheme = strtolower(parse_url($url, PHP_URL_SCHEME));
if (!in_array($scheme, ['http', 'https'], true)) {
    http_response_code(400);
    exit;
}

switch ($format) {
    case 'pls':
        header('Content-Type: audio/x-scpls');
        header('Content-Disposition: attachment; filename="radio.pls"');
        echo "[playlist]\n";
        echo "NumberOfEntries=1\n";
        echo "File1={$url}\n";
        echo "Title1={$name}\n";
        echo "Length1=-1\n";
        echo "Version=2\n";
        break;

    case 'asx':
        header('Content-Type: video/x-ms-asf');
        header('Content-Disposition: attachment; filename="radio.asx"');
        $n = htmlspecialchars($name, ENT_QUOTES | ENT_XML1);
        $u = htmlspecialchars($url,  ENT_QUOTES | ENT_XML1);
        echo "<asx version=\"3.0\">\n";
        echo "  <title>{$n}</title>\n";
        echo "  <entry>\n";
        echo "    <title>{$n}</title>\n";
        echo "    <ref href=\"{$u}\" />\n";
        echo "  </entry>\n";
        echo "</asx>\n";
        break;

    case 'ram':
        header('Content-Type: audio/x-pn-realaudio');
        header('Content-Disposition: attachment; filename="radio.ram"');
        echo $url . "\n";
        break;

    case 'm3u':
    default:
        header('Content-Type: audio/x-mpegurl');
        header('Content-Disposition: attachment; filename="radio.m3u"');
        echo "#EXTM3U\n";
        echo "#EXTINF:-1,{$name}\n";
        echo $url . "\n";
        break;
}
