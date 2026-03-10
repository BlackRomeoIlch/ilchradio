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
             src="<?=$this->getBaseUrl($this->getLayoutSetting('logo')) ?>"
             alt="<?=$this->getLayoutSetting('stationname') ?>">
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
            <audio id="rp-player" controls autoplay>
                <source src="<?=$this->getLayoutSetting('streamurl') ?>" type="audio/mpeg">
                <?=$this->getTrans('browsernotupport') ?>
            </audio>
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
    $metaUrl = $this->getLayoutSetting('metaurl') ?: '';
    $streamUrl = $this->getLayoutSetting('streamurl') ?: '';
    if (empty($metaUrl) && !empty($streamUrl)) {
        $metaUrl = $this->getBaseUrl('application/layouts/ilchradio/icy-proxy.php') . '?url=' . urlencode($streamUrl);
    }
    ?>
    var META_URL = <?=json_encode($metaUrl) ?>;
    var META_KEY = <?=json_encode($this->getLayoutSetting('metakey') ?: 'title') ?>;

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
})();
</script>

</body>
</html>
