<?php

/**
 *    Copyright 2015-2017 ppy Pty. Ltd.
 *
 *    This file is part of osu!web. osu!web is distributed with the hope of
 *    attracting more community contributions to the core ecosystem of osu!.
 *
 *    osu!web is free software: you can redistribute it and/or modify
 *    it under the terms of the Affero GNU General Public License version 3
 *    as published by the Free Software Foundation.
 *
 *    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
 *    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *    See the GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
 */

return [
    'box' => [
        'sent' => 'На почту :mail было отправлено письмо с кодом для подтверждения аккаунта. Введите полученный код.',
        'title' => 'Подтверждение аккаунта',
        'verifying' => 'Проверка...',
        'issuing' => 'Отправка нового кода...',

        'info' => [
            'check_spam' => "Проверьте папку спам, если Вы не можете найти письмо.",
            'recover' => "Если Вы не имеете доступ к своей почты, то пройдите :link.",
            'recover_link' => 'процесс восстановления тут',
            'reissue' => 'Вы также можете :reissue_link или :logout_link.',
            'reissue_link' => 'запросить другой код',
            'logout_link' => 'выйти',
        ],
    ],

    'email' => [
        'subject' => 'osu! подтверждение аккаунта',
    ],

    'errors' => [
        'expired' => 'Время действия кода истёк, на Вашу почту отправлено новое письмо.',
        'incorrect_key' => 'Неверный код подтверждения.',
        'retries_exceeded' => 'Неверный код подтверждения. Вам отправлено новое письмо, в связи с тем, что Вы исчерпали количество попыток.',
        'reissued' => 'Новый код успешно сгенерирован и отправлен Вам на почту.',
        'unknown' => 'Возникла неизвестная ошибка, отправлено новое письмо.',
    ],
];
