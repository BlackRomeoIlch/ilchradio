<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Layouts\Ilchradio\Config;

class Config extends \Ilch\Config\Install
{
    public $config = [
        'name'     => 'Ilch Radio',
        'version'  => '1.0.0',
        'ilchCore' => '2.2.0',
        'author'   => 'Ilch Radio Team',
        'link'     => '',
        'desc'     => 'Modernes Internet-Radio Layout mit Neon-Design',
        'layouts'  => [
            'index_full' => [
                ['module' => 'user', 'controller' => 'panel'],
                ['module' => 'forum'],
                ['module' => 'guestbook'],
            ]
        ],
        'settings' => [

            // ── Allgemein ─────────────────────────────────────
            'sep_general' => [
                'type'        => 'separator',
                'description' => 'Allgemein',
            ],
            'stationname' => [
                'type'        => 'text',
                'default'     => 'Mein Internetradio',
                'description' => 'Name der Radiostation',
            ],
            'streamurl' => [
                'type'        => 'url',
                'default'     => '',
                'description' => 'Stream-URL (MP3/AAC/M3U)',
            ],
            'logo' => [
                'type'        => 'mediaselection',
                'default'     => '',
                'description' => 'Logo (wird im Player angezeigt)',
            ],

            // ── Farben ────────────────────────────────────────
            'sep_colors' => [
                'type'        => 'separator',
                'description' => 'Farben',
            ],
            'color_primary' => [
                'type'        => 'bscolorpicker',
                'default'     => '#c44dff',
                'description' => 'Primärfarbe (Neon-Lila) – Hauptakzent, Links, Glow',
            ],
            'color_secondary' => [
                'type'        => 'bscolorpicker',
                'default'     => '#ff2d9b',
                'description' => 'Sekundärfarbe (Neon-Pink) – ON AIR, Highlights',
            ],
            'color_accent' => [
                'type'        => 'bscolorpicker',
                'default'     => '#7b2dff',
                'description' => 'Akzentfarbe (dunkles Lila) – Gradienten, Equalizer',
            ],
            'color_bg' => [
                'type'        => 'bscolorpicker',
                'default'     => '#06060f',
                'description' => 'Seitenhintergrund',
            ],
            'color_text' => [
                'type'        => 'bscolorpicker',
                'default'     => '#e2e2f0',
                'description' => 'Textfarbe',
            ],
            'color_sidebar_bg' => [
                'type'        => 'bscolorpicker',
                'default'     => '#0b0b18',
                'description' => 'Sidebar-Hintergrundfarbe',
            ],

            // ── Now Playing ───────────────────────────────────
            'sep_nowplaying' => [
                'type'        => 'separator',
                'description' => 'Now Playing (Aktueller Titel)',
            ],
            'metaurl' => [
                'type'        => 'url',
                'default'     => '',
                'description' => 'JSON-API URL für aktuellen Titel (leer = deaktiviert)',
            ],
            'metakey' => [
                'type'        => 'text',
                'default'     => 'title',
                'description' => 'JSON-Pfad zum Titel (Standard: title – bei eigenem API z.B. icestats.source.title)',
            ],

            // ── Player-Bar ────────────────────────────────────
            'sep_player' => [
                'type'        => 'separator',
                'description' => 'Player-Bar',
            ],
            'player_bg_start' => [
                'type'        => 'bscolorpicker',
                'default'     => '#1a003a',
                'description' => 'Player-Hintergrund Startfarbe (links)',
            ],
            'player_bg_end' => [
                'type'        => 'bscolorpicker',
                'default'     => '#0f0030',
                'description' => 'Player-Hintergrund Endfarbe (rechts)',
            ],
            'player_height' => [
                'type'        => 'text',
                'default'     => '80',
                'description' => 'Player-Höhe in Pixel (Standard: 80)',
            ],

            // ── Layout ────────────────────────────────────────
            'sep_layout' => [
                'type'        => 'separator',
                'description' => 'Layout',
            ],
            'sidebar_width' => [
                'type'        => 'text',
                'default'     => '270',
                'description' => 'Sidebar-Breite in Pixel (Standard: 270)',
            ],
            'sidebar_position' => [
                'type'        => 'text',
                'default'     => 'left',
                'description' => 'Sidebar-Position: "left" = Links, "right" = Rechts',
            ],
        ],
    ];

    public function getUpdate($installedVersion)
    {
    }
}
