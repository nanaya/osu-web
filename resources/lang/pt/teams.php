<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

return [
    'applications' => [
        'accept' => [
            'ok' => 'Utilizador adicionado à equipa.',
        ],
        'destroy' => [
            'ok' => 'Pedido de adesão cancelado.',
        ],
        'reject' => [
            'ok' => 'Pedido de adesão rejeitado.',
        ],
        'store' => [
            'ok' => 'Pediste para te juntares à equipa.',
        ],
    ],

    'create' => [
        'submit' => 'Criar equipa',

        'form' => [
            'name_help' => 'O nome da tua equipa. De momento, o nome é permanente.',
            'short_name_help' => 'Máximo 4 caracteres.',
            'title' => "Bora criar uma nova equipa",
        ],

        'intro' => [
            'description' => "Joga com amigos, sejam eles novos ou já existentes. Neste momento, não pertences a nenhuma equipa. Junta-te a uma equipa existente visitando a sua página ou cria a tua própria a partir desta página.",
            'title' => 'Equipa!',
        ],
    ],

    'destroy' => [
        'ok' => 'Equipa removida.',
    ],

    'edit' => [
        'ok' => 'Definições guardadas com êxito.',
        'title' => 'Definições da equipa',

        'description' => [
            'label' => 'Descrição',
            'title' => 'Descrição da equipa',
        ],

        'flag' => [
            'label' => 'Bandeira da equipa',
            'title' => 'Definir bandeira da equipa',
        ],

        'header' => [
            'label' => 'Imagem do cabeçalho',
            'title' => 'Definir imagem do cabeçalho',
        ],

        'settings' => [
            'application_help' => 'Permitir que as pessoas se candidatem à equipa',
            'default_ruleset_help' => 'O conjunto de regras a selecionar por omissão quando se visita a página da equipa',
            'flag_help' => 'Tamanho máximo de :width×:height',
            'header_help' => 'Tamanho máximo de :width×:height',
            'title' => 'Definições da equipa',

            'application_state' => [
                'state_0' => 'Fechadas',
                'state_1' => 'Abertas',
            ],
        ],
    ],

    'header_links' => [
        'edit' => 'definições',
        'leaderboard' => 'tabela de classificação',
        'show' => 'informações',

        'members' => [
            'index' => 'gerir membros',
        ],
    ],

    'leaderboard' => [
        'global_rank' => 'Classificação global',
    ],

    'members' => [
        'destroy' => [
            'success' => 'Membro da equipa removido',
        ],

        'index' => [
            'title' => 'Gerir membros',

            'applications' => [
                'empty' => 'Não há pedidos de adesão de momento.',
                'empty_slots' => 'Espaços disponíveis',
                'title' => 'Pedidos de adesão',
                'created_at' => 'Pedido em',
            ],

            'table' => [
                'status' => 'Estado',
                'joined_at' => 'Data de adesão',
                'remove' => 'Remover',
                'title' => 'Membros atuais',
            ],

            'status' => [
                'status_0' => 'Inativos',
                'status_1' => 'Ativos',
            ],
        ],
    ],

    'part' => [
        'ok' => 'Deixou a equipa ;_;',
    ],

    'show' => [
        'bar' => [
            'chat' => 'Conversa da equipa',
            'destroy' => 'Dissolver equipa',
            'join' => 'Pedir para aderires',
            'join_cancel' => 'Cancelar pedido de adesão',
            'part' => 'Sair da equipa',
        ],

        'info' => [
            'created' => 'Formada em',
        ],

        'members' => [
            'members' => 'Membros da equipa',
            'owner' => 'Líder da equipa',
        ],

        'sections' => [
            'about' => '',
            'info' => 'Informações',
            'members' => 'Membros',
        ],

        'statistics' => [
            'rank' => '',
            'leader' => '',
        ],
    ],

    'store' => [
        'ok' => 'Equipa criada.',
    ],
];
