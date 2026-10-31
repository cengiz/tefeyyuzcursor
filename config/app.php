<?php
declare(strict_types=1);

return [
    'debug' => true,
    'base_url' => 'http://127.0.0.1:8080',
    'timezone' => 'Europe/Istanbul',
    'locale' => 'tr_TR',
    'title' => 'Tefeyyüz',
    'slogan' => 'Bilgiyle yüksel.',
    'description' => 'Tefeyyüz; tarih, bilim, dil, felsefe ve kültür üzerine özenle hazırlanmış ansiklopedi maddeleri ve merak edilen soruların güvenilir cevaplarını bir araya getiren Türkçe bilgi bankasıdır.',
    'author' => 'Tefeyyüz Editörleri',
    'email' => 'iletisim@tefeyyuz.com',
    'twitter' => '',
    'default_image' => '/images/og-default.png',
    'logo_text' => 'Tefeyyüz',
    'per_page' => 12,
    'home_featured' => 4,
    'home_articles' => 8,
    'home_questions' => 8,
    'sqlite_path' => dirname(__DIR__) . '/storage/database/tefeyyuz.sqlite',
    'log_path' => dirname(__DIR__) . '/storage/logs/app.log',
    'session_name' => 'tfz_sid',
    'adsense' => [
        'enabled' => false,
        'client' => 'ca-pub-XXXXXXXXXXXXXXXX',
        'ads_txt' => 'google.com, pub-XXXXXXXXXXXXXXXX, DIRECT, f08c47fec0942fa0',
        'slots' => [
            'header' => '',
            'sidebar' => '',
            'inArticle' => '',
            'inFeed' => '',
            'footer' => '',
        ],
    ],
    'analytics' => [
        'google_tag_id' => '',
    ],
    'verification' => [
        'google' => '',
        'bing' => '',
        'yandex' => '',
    ],
    'menu' => [
        ['name' => 'Ansiklopedi', 'url' => '/ansiklopedi/'],
        ['name' => 'Sorular', 'url' => '/sorular/'],
        ['name' => 'Kategoriler', 'url' => '/kategoriler/'],
        ['name' => 'Soru Sor', 'url' => '/soru-sor/'],
    ],
    'footer_menu' => [
        ['name' => 'Hakkında', 'url' => '/hakkinda/'],
        ['name' => 'İletişim', 'url' => '/iletisim/'],
        ['name' => 'Gizlilik Politikası', 'url' => '/gizlilik-politikasi/'],
        ['name' => 'Kullanım Şartları', 'url' => '/kullanim-sartlari/'],
        ['name' => 'Çerez Politikası', 'url' => '/cerez-politikasi/'],
    ],
];
