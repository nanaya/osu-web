<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

return [
    'play_more' => 'Bunun yerine biraz osu! oynamaya ne dersiniz?',
    'require_login' => 'Devam etmek için lütfen giriş yapın.',
    'require_verification' => 'Devam etmek için lütfen doğrulama işlemini tamamlayın.',
    'restricted' => "Kısıtlanmışken bunu yapamazsınız.",
    'silenced' => "Susturulmuşken bunu yapamazsınız.",
    'unauthorized' => 'Erişim engellendi.',

    'beatmap_discussion' => [
        'destroy' => [
            'is_hype' => 'Verilen gaz geri alınamaz.',
            'has_reply' => 'Cevaplara sahip tartışmalar silinemez',
        ],
        'nominate' => [
            'exhausted' => 'Bugünkü aday gösterme sınırınıza ulaştınız, lütfen yarın tekrar deneyin.',
            'incorrect_state' => 'Bu eylemi gerçekleştirirken bir hata oluştu, sayfayı yenilemeyi deneyin.',
            'owner' => "Kendi beatmapinizi aday gösteremezsiniz.",
            'set_metadata' => 'Aday göstermeden önce türü ve dili ayarlamalısınız.',
        ],
        'resolve' => [
            'not_owner' => 'Yalnızca başlık sahibi ile beatmap sahibi bir tartışmayı sonlandırabilir.',
        ],

        'store' => [
            'mapper_note_wrong_user' => 'Yalnızca beatmap sahibi ya da aday gösterici/NAT grup üyesi mapper notu gönderebilir.',
        ],

        'vote' => [
            'bot' => "Bot tarafından açılan tartışmada oy kullanılamaz",
            'limit_exceeded' => 'Lütfen daha fazla oy vermeden önce bir süre bekleyin',
            'owner' => "Kendi tartışmanıza oy veremezsiniz.",
            'wrong_beatmapset_state' => 'Yalnızca beklemede olan beatmaplerin tartışmalarında oy kullanabilirsiniz.',
        ],
    ],

    'beatmap_discussion_post' => [
        'destroy' => [
            'not_owner' => 'Yalnızca kendi gönderilerinizi silebilirsiniz.',
            'resolved' => 'Çözülmüş bir tartışmanın gönderisini silemezsiniz.',
            'system_generated' => 'Otomatik olarak oluşturulan gönderiler silinemez.',
        ],

        'edit' => [
            'not_owner' => 'Gönderileri yalnızca gönderen düzenleyebilir.',
            'resolved' => 'Çözülmüş bir tartışmanın gönderisini düzenleyemezsiniz.',
            'system_generated' => 'Otomatik olarak oluşturulmuş gönderiler düzenlenemez.',
        ],
    ],

    'beatmapset' => [
        'discussion_locked' => 'Bu harita tartışma için kilitlenmiştir.',

        'metadata' => [
            'nominated' => 'Aday gösterilen bir mapin metaverisini değiştiremezsiniz. Hatalı ayarlandığını düşünüyorsanız bir BN ya da NAT üyesiyle iletişime geçiniz.',
        ],
    ],

    'beatmap_tag' => [
        'store' => [
            'no_score' => 'Beatmape etiket eklemek için önce oynaman gerek.',
        ],
    ],

    'chat' => [
        'blocked' => 'Sizi engelleyen ya da sizin engellediğiniz bir kullanıcıya mesaj gönderemezsiniz.',
        'friends_only' => 'Kullanıcı arkadaş listesinde bulunmayan kişilerden gelen mesajları engelliyor.',
        'moderated' => 'O kanal şu anda modere ediliyor.',
        'no_access' => 'Bu kanala erişiminiz yok.',
        'no_announce' => 'Duyuru paylaşmaya yetkiniz yok.',
        'receive_friends_only' => 'Yalnızca arkadaş listenizdeki kişilerden gelen mesajları kabul ettiğiniz için kullanıcı yanıt veremeyebilir.',
        'restricted' => 'Susturulmuş, kısıtlanmış ya da banlanmış iken mesaj gönderemezsiniz.',
        'silenced' => 'Susturulmuşken, kısıtlıyken veya banlıyken mesaj gönderemezsiniz.',
    ],

    'comment' => [
        'store' => [
            'disabled' => 'Yorumlar devre dışı',
        ],
        'update' => [
            'deleted' => "Silinmiş gönderi düzenlenemez.",
        ],
    ],

    'contest' => [
        'judging_not_active' => 'Bu yarışma için değerlendirme aktif değil.',
        'voting_over' => 'Yarışma için oylama süresi bittikten sonra oyunuzu değiştiremezsiniz.',

        'entry' => [
            'limit_reached' => 'Bu yarışma için girdi sınırına ulaştınız',
            'over' => 'Girdileriniz için teşekkürler! Bu yarışma için gönderiler kapandı ve yakında oylamalara açılacak.',
        ],
    ],

    'forum' => [
        'moderate' => [
            'no_permission' => 'Bu forumu modere etme yetkiniz yok.',
        ],

        'post' => [
            'delete' => [
                'only_last_post' => 'Yalnızca son gönderi silinebilir.',
                'locked' => 'Kilitli bir konunun gönderisini silemezsiniz.',
                'no_forum_access' => 'İstenilen foruma erişim gereklidir.',
                'not_owner' => 'Gönderi yalnızca gönderen tarafından silinebilir.',
            ],

            'edit' => [
                'deleted' => 'Silinmiş gönderi düzenlenemez.',
                'locked' => 'Bu gönderi için düzenleme yasaklanmıştır.',
                'no_forum_access' => 'İstenilen foruma erişim gereklidir.',
                'not_owner' => 'Gönderi yalnızca gönderen tarafından düzenlenebilir.',
                'topic_locked' => 'Kilitli bir konunun gönderisini düzenleyemezsiniz.',
            ],

            'store' => [
                'play_more' => 'Lütfen forumlara başlık açmadan önce oyunu oynamayı deneyin! Eğer oynamakla ilgili bir sorununuz varsa, Help and Support forumuna başlık açın.',
                'too_many_help_posts' => "Başka başlıklar açmadan önce oyunu daha fazla oynamalısınız. Eğer hala oyunu oynamakta sorun yaşıyorsanız, support@ppy.sh adresine e-posta atın", // FIXME: unhardcode email address.
            ],
        ],

        'topic' => [
            'reply' => [
                'double_post' => 'Lütfen yeni gönderi yerine son gönderinizi düzenleyin.',
                'locked' => 'Kilitli bir başlığa cevap yazamazsınız.',
                'no_forum_access' => 'İstenilen foruma erişim gereklidir.',
                'no_permission' => 'Cevaplama izni yok.',

                'user' => [
                    'require_login' => 'Cevaplamak için lütfen giriş yapın.',
                    'restricted' => "Kısıtlanmışken cevap yazamazsınız.",
                    'silenced' => "Susturulmuşken cevap yazamazsınız.",
                ],
            ],

            'store' => [
                'no_forum_access' => 'İstenilen foruma erişim gereklidir.',
                'no_permission' => 'Yeni başlık açmak için izniniz yok.',
                'forum_closed' => 'Forum kapalıdır ve gönderi yapılamaz.',
            ],

            'vote' => [
                'no_forum_access' => 'İstenilen foruma erişim gereklidir.',
                'over' => 'Oylama bitti ve artık oy verilemez.',
                'play_more' => 'Forumda oylama yapmadan önce daha çok oynamanız gerekmektedir.',
                'voted' => 'Oy değiştirmek yasaktır.',

                'user' => [
                    'require_login' => 'Oy vermek için lütfen giriş yapın.',
                    'restricted' => "Kısıtlanmışken oy veremezsiniz.",
                    'silenced' => "Susturulmuşken oy veremezsiniz.",
                ],
            ],

            'watch' => [
                'no_forum_access' => 'İstenilen foruma erişim gereklidir.',
            ],
        ],

        'topic_cover' => [
            'edit' => [
                'uneditable' => 'Geçersiz kapak belirtildi.',
                'not_owner' => 'Yalnızca başlık sahibi kapağı değiştirebilir.',
            ],
            'store' => [
                'forum_not_allowed' => 'Bu forum kapak resmi kabul etmemektedir.',
            ],
        ],

        'view' => [
            'admin_only' => 'Yalnızca yönetici bu forumu görüntüleyebilir.',
        ],
    ],

    'room' => [
        'destroy' => [
            'not_owner' => 'Sadece oda sahibi kapatabilir.',
        ],
    ],

    'score' => [
        'pin' => [
            'disabled_type' => "Bu tür skorlar sabitlenemez",
            'failed' => "Geçmeyen skor sabitlenemiyor.",
            'not_owner' => 'Sadece skor sahibi skoru sabitleyebilir.',
            'too_many' => 'Çok fazla skor sabitlendi.',
        ],
    ],

    'team' => [
        'application' => [
            'store' => [
                'already_member' => "Zaten takımın bir parçasısın.",
                'already_other_member' => "Halihazırda başka bir takıma üyesin.",
                'currently_applying' => 'Bekleyen bir takıma giriş isteğin var.',
                'team_closed' => 'Takım şu anda girme isteklerini kabul etmiyor.',
                'team_full' => "Takım dolu ve daha fazla üye alamaz.",
            ],
        ],
        'part' => [
            'is_leader' => "Takım lideri takımı terk edemez.",
            'not_member' => 'Takımın üyesi değil.',
        ],
        'store' => [
            'require_supporter_tag' => 'Bir takım yaratabilmek için osu!supporter etiketine sahip olman gerek.',
        ],
    ],

    'user' => [
        'page' => [
            'edit' => [
                'locked' => 'Kullanıcı sayfası kilitli.',
                'not_owner' => 'Yalnızca kendi kullanıcı sayfanızı düzenleyebilirsiniz.',
                'require_supporter_tag' => 'osu!supporter etiketi gereklidir.',
            ],
        ],
        'update_email' => [
            'locked' => 'e-posta adresi kilitlenmiş durumda',
        ],
    ],
];
