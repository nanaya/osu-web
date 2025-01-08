<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

return [
    'page_description' => 'osu! Izvēlētie mākslinieki',
    'title' => 'Izvēlētie Mākslinieki',

    'admin' => [
        'hidden' => 'PAŠLAIK MĀKSLINIEKS IR PASLĒPTS',
    ],

    'beatmaps' => [
        '_' => 'Ritma-mapes',
        'download' => 'Lejupielādēt Bītmapes Veidni',
        'download-na' => 'Bītmapes Veidne vēl nav pieejama',
    ],

    'index' => [
        'description' => 'Izvēlētie mākslinieki ir mākslinieki, ar kuriem mēs sadarbojamies, lai osu! piedāvātu jaunu un oriģinālu mūziku. Šos māksliniekus un viņu dziesmu izlasi ir atlasījusi osu! komanda, jo tās ir lieliskas un piemērotas ritma-mapju veidošanai. Daži no šiem Izvēlētajiem Māksliniekiem ir radījuši arī ekskluzīvas jaunas dziesmas izmantošanai osu!.<br><br>Visas šajā sadaļā iekļautās dziesmas ir nodrošinātas kā pirmatnēji .osz faili un ir oficiāli licencētas izmantošanai osu! un ar osu! saistītā saturā.',
    ],

    'links' => [
        'beatmaps' => 'osu! Ritma-Mapes',
        'osu' => 'osu! profils',
        'site' => 'Oficiālā tīmekļa vietne',
    ],

    'songs' => [
        '_' => 'Dziesmas',
        'count' => ':count_delimited dziesma|:count_delimited dziesmas',
        'original' => 'osu! oriģināli',
        'original_badge' => 'ORIGINAL',
    ],

    'tracklist' => [
        'title' => 'nosaukums',
        'length' => 'garums',
        'bpm' => 'bpm',
        'genre' => 'žanrs',
    ],

    'tracks' => [
        'index' => [
            '_' => 'dziesmu meklēšana',

            'exclusive_only' => [
                'all' => ' Viss',
                'exclusive_only' => 'osu! oriģināls',
            ],

            'form' => [
                'advanced' => 'Paplašināta Meklēšana',
                'album' => 'Albums',
                'artist' => 'Mākslinieks',
                'bpm_gte' => 'BPM Minimums',
                'bpm_lte' => 'BPM Maksimums',
                'empty' => 'Netika atrasta neviena dziesma, kas atbilstu meklēšanas kritērijiem.',
                'exclusive_only' => 'Tips',
                'genre' => 'Žanrs',
                'genre_all' => 'Viss',
                'length_gte' => 'Minimālais Garums',
                'length_lte' => 'Maksimālais Garums',
            ],
        ],
    ],
];
