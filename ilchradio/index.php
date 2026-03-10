<?php

/** @var $this \Ilch\Layout\Frontend */
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <?=$this->getHeader() ?>
    <link href="<?=$this->getLayoutUrl('style.css') ?>" rel="stylesheet">
    <style>
        :root {
            --neon:         <?=$this->getLayoutSetting('color_primary') ?: '#c44dff' ?>;
            --neon2:        <?=$this->getLayoutSetting('color_secondary') ?: '#ff2d9b' ?>;
            --neon3:        <?=$this->getLayoutSetting('color_accent') ?: '#7b2dff' ?>;
            --bg:           <?=$this->getLayoutSetting('color_bg') ?: '#06060f' ?>;
            --text:         <?=$this->getLayoutSetting('color_text') ?: '#e2e2f0' ?>;
            --bg2:          <?=$this->getLayoutSetting('color_sidebar_bg') ?: '#0b0b18' ?>;
            --player-h:     <?=(int)($this->getLayoutSetting('player_height') ?: 80) ?>px;
            --sidebar-w:    <?=(int)($this->getLayoutSetting('sidebar_width') ?: 270) ?>px;
            --player-start: <?=$this->getLayoutSetting('player_bg_start') ?: '#1a003a' ?>;
            --player-end:   <?=$this->getLayoutSetting('player_bg_end') ?: '#0f0030' ?>;
        }
        #rp-bar {
            background: linear-gradient(135deg, var(--player-start) 0%, var(--neon3) 40%, var(--player-end) 100%);
        }
        <?php if ($this->getLayoutSetting('sidebar_position') === 'right'): ?>
        #rp-aside { order: 3; border-right: none; border-left: 1px solid rgba(var(--neon-rgb, 196,77,255),.2); }
        #rp-main  { order: 2; }
        <?php endif; ?>
    </style>
    <?=$this->getCustomCSS() ?>
</head>
<body>

<!-- Hintergrund-Effekte -->
<div id="rp-bg" aria-hidden="true">
    <div class="rp-orb rp-orb-1"></div>
    <div class="rp-orb rp-orb-2"></div>
    <div class="rp-orb rp-orb-3"></div>
    <div class="rp-grid"></div>
    <div class="rp-scanline"></div>
</div>

<!-- ====================================================
     PLAYER BAR (fixiert oben)
     ==================================================== -->
<div id="rp-bar">

    <?php if ($this->getLayoutSetting('logo')): ?>
        <img class="rp-logo"
             src="<?=$this->getBaseUrl(str_replace(' ', '%20', $this->getLayoutSetting('logo'))) ?>"
             alt="<?=htmlspecialchars($this->getLayoutSetting('stationname')) ?>">
    <?php endif; ?>

    <span class="rp-name"><?=$this->getLayoutSetting('stationname') ?: 'Ilch Radio' ?></span>

    <div class="rp-sep"></div>

    <?php if ($this->getLayoutSetting('streamurl')): ?>

        <div class="rp-live">
            <div class="onair-dot"></div>
            <span class="onair-label">On Air</span>
            <div class="rp-eq">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>

        <div class="rp-sep"></div>

        <span class="rp-nowplaying">
            <strong><?=$this->getTrans('nowplaying') ?>:</strong>
            &nbsp;<span id="rp-track"><?=$this->getTrans('livestream') ?></span>
        </span>

        <div class="rp-sep"></div>

        <div class="rp-audio">
            <?php
            $streamUrl = $this->getLayoutSetting('streamurl');
            $ext = strtolower(pathinfo(parse_url($streamUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
            $mimeMap = ['ogg' => 'audio/ogg', 'aac' => 'audio/aac', 'mp4' => 'audio/mp4', 'opus' => 'audio/ogg; codecs=opus'];
            $mimeType = $mimeMap[$ext] ?? 'audio/mpeg';
            ?>
            <audio id="rp-player" controls autoplay>
                <source src="<?=$streamUrl ?>" type="<?=$mimeType ?>">
                <?=$this->getTrans('browsernotupport') ?>
            </audio>
        </div>

    <?php else: ?>
        <span class="rp-nostream"><?=$this->getTrans('nostream') ?></span>
    <?php endif; ?>

</div>

<!-- ====================================================
     SEITEN-LAYOUT
     ==================================================== -->
<div id="rp-page">

    <!-- SIDEBAR (links) – Navigation + Widgets -->
    <aside id="rp-aside">

        <!-- Studio / Uhr Widget (an erster Stelle) -->
        <div class="rp-studio-widget">
            <div class="studio-label" id="studio-station">• • •</div>
            <div class="studio-eq">
                <span></span><span></span><span></span><span></span>
                <span></span><span></span><span></span>
            </div>
            <div id="studio-time">00:00:00</div>
            <div id="studio-date"></div>
        </div>

        <!-- Hauptnavigation -->
        <?php
        echo $this->getMenu(
            1,
            '<div class="rp-card">
                 <div class="rp-card-head">%s</div>
                 <div class="rp-card-body">%c</div>
             </div>'
        );
        ?>

        <!-- Menü 2 (Boxen / Widgets) -->
        <?php
        echo $this->getMenu(
            2,
            '<div class="rp-card">
                 <div class="rp-card-head">%s</div>
                 <div class="rp-card-body">%c</div>
             </div>'
        );
        ?>

    </aside>

    <!-- MAIN CONTENT -->
    <main id="rp-main">
        <?=$this->getHmenu() ?>
        <div class="rp-content-card">
            <?=$this->getContent() ?>
        </div>
    </main>

</div>

<!-- ====================================================
     FOOTER
     ==================================================== -->
<footer id="rp-footer">
    &copy; <?=date('Y') ?> <?=$this->getLayoutSetting('stationname') ?: 'Ilch Radio' ?> &ndash;
    <a href="<?=$this->getUrl() ?>"><?=$this->getTrans('home') ?></a> |
    <a href="<?=$this->getUrl(['module' => 'contact', 'controller' => 'index', 'action' => 'index']) ?>"><?=$this->getTrans('contact') ?></a> |
    <a href="<?=$this->getUrl(['module' => 'imprint', 'controller' => 'index', 'action' => 'index']) ?>"><?=$this->getTrans('imprint') ?></a> |
    <a href="<?=$this->getUrl(['module' => 'privacy', 'controller' => 'index', 'action' => 'index']) ?>"><?=$this->getTrans('privacy') ?></a>
</footer>

<?=$this->getFooter() ?>

<!-- LIVE-UHR + NOW PLAYING JavaScript -->
<script>
(function () {
    // ── Uhr ────────────────────────────────────────
    var DAYS   = ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'];
    var MONTHS = ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'];
    function pad(n) { return n < 10 ? '0' + n : n; }

    function tick() {
        var now  = new Date();
        var time = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        var date = DAYS[now.getDay()] + ', ' + now.getDate() + '. ' + MONTHS[now.getMonth()] + ' ' + now.getFullYear();

        var elStTime = document.getElementById('studio-time');
        var elStDate = document.getElementById('studio-date');

        if (elStTime) elStTime.textContent = time;
        if (elStDate) elStDate.textContent = date;
    }
    tick();
    setInterval(tick, 1000);

    // ── Now Playing ────────────────────────────────
    <?php
    $metaUrl    = $this->getLayoutSetting('metaurl') ?: '';
    $streamUrl  = $this->getLayoutSetting('streamurl') ?: '';
    $jsonProxy  = $this->getBaseUrl('application/layouts/ilchradio/json-proxy.php');
    $icyProxy   = $this->getBaseUrl('application/layouts/ilchradio/icy-proxy.php');

    if (!empty($metaUrl)) {
        // Externe JSON-API → über PHP-Proxy routen (CORS-Bypass)
        $fetchUrl = $jsonProxy . '?url=' . urlencode($metaUrl);
        $useIcy   = false;
    } elseif (!empty($streamUrl)) {
        // Kein Meta-URL → ICY-Stream-Proxy
        $fetchUrl = $icyProxy . '?url=' . urlencode($streamUrl);
        $useIcy   = true;
    } else {
        $fetchUrl = '';
        $useIcy   = false;
    }
    ?>
    var FETCH_URL = <?=json_encode($fetchUrl) ?>;
    var META_KEY  = <?=json_encode($this->getLayoutSetting('metakey') ?: 'title') ?>;
    var USE_ICY   = <?=$useIcy ? 'true' : 'false' ?>;

    // Unterstützt Punkt-Notation inkl. Array-Indizes: "items.0.title"
    function getNestedValue(obj, path) {
        return path.split('.').reduce(function(o, k) {
            if (o === null || o === undefined) return null;
            // Numerischen Index für Arrays behandeln
            var idx = parseInt(k, 10);
            return Array.isArray(o) && !isNaN(idx) ? o[idx] : o[k];
        }, obj);
    }

    function updateNowPlaying() {
        if (!FETCH_URL) return;
        fetch(FETCH_URL + (FETCH_URL.indexOf('?') === -1 ? '?' : '&') + '_=' + Date.now())
            .then(function(r) { return r.json(); })
            .then(function(data) {
                // Songtitel
                var title = getNestedValue(data, META_KEY);
                var elTrack = document.getElementById('rp-track');
                if (elTrack && title) {
                    elTrack.textContent = title;
                    elTrack.title = title;
                }
                // Sendername (nur bei ICY-Proxy vorhanden)
                if (USE_ICY && data.station) {
                    var elStation = document.getElementById('studio-station');
                    if (elStation) elStation.textContent = data.station;
                }
            })
            .catch(function() {});
    }

    if (FETCH_URL) {
        updateNowPlaying();
        setInterval(updateNowPlaying, 30000); // alle 30 Sekunden
    }
})();
</script>

</body>
</html>
