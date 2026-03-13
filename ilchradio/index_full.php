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
            --player-start: <?=$this->getLayoutSetting('player_bg_start') ?: '#1a003a' ?>;
            --player-end:   <?=$this->getLayoutSetting('player_bg_end') ?: '#0f0030' ?>;
        }
        #rp-bar {
            background: linear-gradient(135deg, var(--player-start) 0%, var(--neon3) 40%, var(--player-end) 100%);
        }
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

<!-- PLAYER BAR -->
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
            <audio id="rp-player">
                <source src="<?=$streamUrl ?>" type="<?=$mimeType ?>">
            </audio>

            <!-- Play / Pause -->
            <button class="rp-play-btn" id="rp-play" aria-label="Play / Pause">
                <svg class="icon-play" viewBox="0 0 20 20" width="15" height="15" aria-hidden="true"><polygon points="4,2 18,10 4,18" fill="currentColor"/></svg>
                <svg class="icon-pause" viewBox="0 0 20 20" width="15" height="15" aria-hidden="true"><rect x="3" y="2" width="5" height="16" rx="1" fill="currentColor"/><rect x="12" y="2" width="5" height="16" rx="1" fill="currentColor"/></svg>
            </button>

            <!-- Mute -->
            <button class="rp-mute-btn" id="rp-mute" aria-label="Ton">
                <svg class="icon-vol" viewBox="0 0 20 20" width="14" height="14" aria-hidden="true"><path d="M3 7h4l5-4v14l-5-4H3z" fill="currentColor"/><path d="M14 6q3 4 0 8" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                <svg class="icon-muted" viewBox="0 0 20 20" width="14" height="14" aria-hidden="true"><path d="M3 7h4l5-4v14l-5-4H3z" fill="currentColor"/><line x1="14" y1="7" x2="19" y2="13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><line x1="19" y1="7" x2="14" y2="13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </button>

            <!-- Volume -->
            <input type="range" class="rp-vol" id="rp-vol" min="0" max="1" step="0.02" value="1" aria-label="Lautstärke">
        </div>

        <div class="rp-ext-players">
            <?php
            $plBase = $this->getBaseUrl('application/layouts/ilchradio/playlist.php');
            $plName = urlencode($this->getLayoutSetting('stationname') ?: 'Radio');
            $plUrl  = urlencode($streamUrl);
            ?>
            <a class="rp-ext-btn" href="<?=$plBase?>?format=m3u&url=<?=$plUrl?>&name=<?=$plName?>" title="VLC Media Player" download="radio.m3u">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="20" height="20" aria-hidden="true">
                    <polygon points="50,6 94,82 6,82" fill="#ff8c00"/>
                    <rect x="28" y="70" width="44" height="13" rx="3" fill="#cc6a00"/>
                    <rect x="36" y="84" width="28" height="10" rx="2" fill="#cc6a00"/>
                </svg>
            </a>
            <a class="rp-ext-btn" href="<?=$plBase?>?format=pls&url=<?=$plUrl?>&name=<?=$plName?>" title="Winamp" download="radio.pls">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                    <rect width="24" height="24" rx="4" fill="#1a1a2e"/>
                    <path d="M4 7 L7 17 L10 11 L12 15 L14 11 L17 17 L20 7" fill="none" stroke="#00e676" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round"/>
                </svg>
            </a>
            <a class="rp-ext-btn" href="<?=$plBase?>?format=asx&url=<?=$plUrl?>&name=<?=$plName?>" title="Windows Media Player" download="radio.asx">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                    <circle cx="12" cy="12" r="11" fill="#0078d4"/>
                    <polygon points="9,7 19,12 9,17" fill="white"/>
                </svg>
            </a>
            <a class="rp-ext-btn" href="<?=$plBase?>?format=ram&url=<?=$plUrl?>&name=<?=$plName?>" title="RealPlayer" download="radio.ram">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true">
                    <circle cx="12" cy="12" r="11" fill="#003f72"/>
                    <path d="M8 7 h4 a3.5 3.5 0 0 1 0 7 h-4 z M12 14 L16 17" fill="none" stroke="#ffd700" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </a>
        </div>

    <?php else: ?>
        <span class="rp-nostream"><?=$this->getTrans('nostream') ?></span>
    <?php endif; ?>

</div>

<!-- VOLLBREITE INHALT -->
<div id="rp-page">
    <main id="rp-main" style="width:100%;">
        <?=$this->getHmenu() ?>
        <div class="rp-content-card">
            <?=$this->getContent() ?>
        </div>
    </main>
</div>

<!-- FOOTER -->
<footer id="rp-footer">
    &copy; <?=date('Y') ?> <?=$this->getLayoutSetting('stationname') ?: 'Ilch Radio' ?> &ndash;
    <a href="<?=$this->getUrl() ?>"><?=$this->getTrans('home') ?></a> |
    <a href="<?=$this->getUrl(['module' => 'contact', 'controller' => 'index', 'action' => 'index']) ?>"><?=$this->getTrans('contact') ?></a> |
    <a href="<?=$this->getUrl(['module' => 'imprint', 'controller' => 'index', 'action' => 'index']) ?>"><?=$this->getTrans('imprint') ?></a> |
    <a href="<?=$this->getUrl(['module' => 'privacy', 'controller' => 'index', 'action' => 'index']) ?>"><?=$this->getTrans('privacy') ?></a>
</footer>

<?=$this->getFooter() ?>

<script>
(function () {
    function pad(n) { return n < 10 ? '0' + n : n; }
    function tick() {
        var now = new Date();
        var el  = document.getElementById('rp-clock');
        if (el) el.textContent = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
    }
    tick(); setInterval(tick, 1000);

    <?php
    $serverType = $this->getLayoutSetting('servertype') ?: 'icy';
    $metaUrl    = $this->getLayoutSetting('metaurl') ?: '';
    $streamUrl  = $this->getLayoutSetting('streamurl') ?: '';
    $jsonProxy  = $this->getBaseUrl('application/layouts/ilchradio/json-proxy.php');
    $icyProxy   = $this->getBaseUrl('application/layouts/ilchradio/icy-proxy.php');

    $fetchUrl     = '';
    $metaKeyFinal = $this->getLayoutSetting('metakey') ?: 'title';

    if ($serverType === 'shoutcast2' && !empty($streamUrl)) {
        $p        = parse_url($streamUrl);
        $base     = $p['scheme'] . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
        $fetchUrl = $jsonProxy . '?url=' . urlencode($base . '/stats?json=1');
        $metaKeyFinal = 'songtitle';
    } elseif ($serverType === 'icecast_json' && !empty($streamUrl)) {
        $p        = parse_url($streamUrl);
        $base     = $p['scheme'] . '://' . $p['host'] . (isset($p['port']) ? ':' . $p['port'] : '');
        $fetchUrl = $jsonProxy . '?url=' . urlencode($base . '/status-json.xsl');
        $metaKeyFinal = 'icestats.source.title';
    } elseif ($serverType === 'manual' && !empty($metaUrl)) {
        $fetchUrl = $jsonProxy . '?url=' . urlencode($metaUrl);
    } elseif (!empty($streamUrl)) {
        // icy (Standard) oder Fallback
        $fetchUrl = $icyProxy . '?url=' . urlencode($streamUrl);
        $metaKeyFinal = 'title';
    }
    ?>
    var META_URL = <?=json_encode($fetchUrl) ?>;
    var META_KEY = <?=json_encode($metaKeyFinal) ?>;

    function getNestedValue(obj, path) {
        return path.split('.').reduce(function(o, k) { return (o && o[k] !== undefined) ? o[k] : null; }, obj);
    }
    function updateNowPlaying() {
        if (!META_URL) return;
        fetch(META_URL + (META_URL.indexOf('?') === -1 ? '?' : '&') + '_=' + Date.now())
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var title = getNestedValue(data, META_KEY);
                var el = document.getElementById('rp-track');
                if (el && title) { el.textContent = title; el.title = title; }
            })
            .catch(function() {});
    }
    if (META_URL) { updateNowPlaying(); setInterval(updateNowPlaying, 30000); }

    // ── Custom Player Controls ──────────────────────
    var audio     = document.getElementById('rp-player');
    var btnPlay   = document.getElementById('rp-play');
    var btnMute   = document.getElementById('rp-mute');
    var volSlider = document.getElementById('rp-vol');

    if (audio && btnPlay) {
        function syncPlayBtn() {
            btnPlay.classList.toggle('playing', !audio.paused);
        }
        audio.addEventListener('play',  syncPlayBtn);
        audio.addEventListener('pause', syncPlayBtn);

        btnPlay.addEventListener('click', function () {
            if (audio.paused) { audio.play(); } else { audio.pause(); }
        });

        if (btnMute) {
            btnMute.addEventListener('click', function () {
                audio.muted = !audio.muted;
                btnMute.classList.toggle('muted', audio.muted);
                if (volSlider) volSlider.value = audio.muted ? 0 : audio.volume;
            });
        }

        if (volSlider) {
            volSlider.addEventListener('input', function () {
                audio.volume = parseFloat(this.value);
                var muted = parseFloat(this.value) === 0;
                audio.muted = muted;
                if (btnMute) btnMute.classList.toggle('muted', muted);
            });
        }
    }
})();
</script>

</body>
</html>
