<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

return [
    'create' => [
        'submit' => 'Create Team',

        'form' => [
            'name' => 'Team Name',
            'name_help' => 'Your team name. No changey',
            'short_name' => 'Short Name',
            'short_name_help' => 'What',
            'title' => "Let's set up a new team",
        ],

        'intro' => [
            'description' => "You're not currently in a team! Join a team or create your own team from this page.",
            'title' => 'Team!',
        ],
    ],

    'destroy' => [
        'ok' => 'Team removed',
    ],

    'edit' => [
        'saved' => 'Settings saved successfully',
        'title' => 'Team Settings',

        'description' => [
            'label' => 'Description',
            'title' => 'Team Description',
        ],

        'header' => [
            'label' => 'Header Image',
            'title' => 'Set Header Image',
        ],

        'logo' => [
            'label' => 'Team Flag',
            'title' => 'Set Team Flag',
        ],

        'settings' => [
            'application' => 'Team Application',
            'application_help' => 'Whether to allow people to apply for the team',
            'default_ruleset' => 'Default Ruleset',
            'default_ruleset_help' => 'The ruleset to be selected by default when visiting the team page',
            'title' => 'Team Settings',
            'url' => 'URL',

            'application_state' => [
                'state_0' => 'Closed',
                'state_1' => 'Open',
            ],
        ],
    ],

    'members' => [
        'destroy' => [
            'success' => 'Team member removed',
        ],

        'index' => [
            'title' => 'Manage Members',

            'table' => [
                'status' => 'Status',
                'joined_at' => 'Join Date',
                'remove' => 'Remove',
                'title' => 'Current Members',
            ],

            'status' => [
                'status_0' => 'Inactive',
                'status_1' => 'Active',
            ],
        ],
    ],

    'part' => [
        'ok' => 'Left the team ;_;',
    ],

    'show' => [
        'bar' => [
            'destroy' => 'Disband Team',
            'part' => 'Leave Team',
        ],

        'info' => [
            'created' => 'Formed',
            'website' => 'Website',
        ],

        'members' => [
            'members' => 'Team Members',
            'owner' => 'Team Leader',
        ],

        'sections' => [
            'members' => 'Members',
            'info' => 'Info',
        ],
    ],
];
