<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

return [
    'applications' => [
        'accept' => [
            'ok' => 'Đã thêm thành viên vào đội.',
        ],
        'destroy' => [
            'ok' => 'Đã huỷ yêu cầu gia nhập.',
        ],
        'reject' => [
            'ok' => 'Đã từ chối yêu cầu gia nhập.',
        ],
        'store' => [
            'ok' => 'Đã yêu cầu gia nhập vào đội.',
        ],
    ],

    'create' => [
        'submit' => 'Tạo đội',

        'form' => [
            'name_help' => '',
            'short_name_help' => 'Tối đa 4 kí tự.',
            'title' => "Hãy thành lập một đội mới nào",
        ],

        'intro' => [
            'description' => "",
            'title' => 'Đội!',
        ],
    ],

    'destroy' => [
        'ok' => 'Nhóm đã bị xóa',
    ],

    'edit' => [
        'ok' => 'Lưu cài đặt thành công.',
        'title' => 'Cài đặt đội',

        'description' => [
            'label' => 'Mô tả',
            'title' => 'Mô tả đội',
        ],

        'flag' => [
            'label' => 'Cờ đội',
            'title' => 'Đặt cờ đội',
        ],

        'header' => [
            'label' => 'Ảnh bìa',
            'title' => 'Đặt ảnh bìa',
        ],

        'settings' => [
            'application_help' => 'Tùy chọn cho phép mọi người vào đội',
            'default_ruleset_help' => 'Quy tắc sẽ được chọn làm mặc định khi xem trang của đội',
            'flag_help' => 'Kích cỡ tối đa là :width×:height',
            'header_help' => 'Kích cỡ tối đa là :width×:height',
            'title' => 'Cài đặt đội',

            'application_state' => [
                'state_0' => 'Đã đóng',
                'state_1' => 'Mở',
            ],
        ],
    ],

    'header_links' => [
        'edit' => 'cài đặt',
        'leaderboard' => 'bảng xếp hạng',
        'show' => 'thông tin',

        'members' => [
            'index' => 'quản lý thành viên',
        ],
    ],

    'leaderboard' => [
        'global_rank' => 'Xếp hạng toàn cầu',
    ],

    'members' => [
        'destroy' => [
            'success' => 'Đã loại thành viên đội',
        ],

        'index' => [
            'title' => 'Quản lý thành viên',

            'applications' => [
                'empty' => 'Hiện nay không có yêu cầu gia nhập.',
                'empty_slots' => 'Số chỗ còn trống',
                'title' => 'Yêu cầu gia nhập',
                'created_at' => 'Yêu cầu vào lúc',
            ],

            'table' => [
                'status' => 'Trạng thái',
                'joined_at' => 'Ngày tham gia',
                'remove' => 'Loại',
                'title' => 'Thành viên hiện tại',
            ],

            'status' => [
                'status_0' => 'Không hoạt động',
                'status_1' => 'Hoạt động',
            ],
        ],
    ],

    'part' => [
        'ok' => 'Đã rời đội ;_;',
    ],

    'show' => [
        'bar' => [
            'chat' => 'Trò chuyện đội',
            'destroy' => 'Giải tán đội',
            'join' => 'Yêu cầu gia nhập',
            'join_cancel' => 'Huỷ gia nhập',
            'part' => 'Rời đội',
        ],

        'info' => [
            'created' => 'Đã hình thành',
        ],

        'members' => [
            'members' => 'Thành viên đội',
            'owner' => 'Trưởng đội',
        ],

        'sections' => [
            'about' => '',
            'info' => 'Thông tin',
            'members' => 'Thành viên',
        ],

        'statistics' => [
            'rank' => '',
            'leader' => '',
        ],
    ],

    'store' => [
        'ok' => 'Đội đã được tạo.',
    ],
];
