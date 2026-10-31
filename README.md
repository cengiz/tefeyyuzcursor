# Tefeyyüz — tefeyyuz.com

Türkçe ansiklopedi + soru-cevap bilgi bankası. **PHP 8** ve **SQLite** ile çalışır; ucuz paylaşımlı hostingde barınır.

Hugo prototipi (`layouts/`, `content/`, `hugo.toml`) içerik kaynağı olarak durur. Çalışan uygulama `public/`, `src/`, `templates/` ve `storage/` altındadır.

## Gereksinimler

- PHP 8.1+ (`pdo_sqlite`, `mbstring`, `openssl`)
- Yazılabilir `storage/database/` ve `storage/logs/`

Yerelde `C:\tools\php\php.exe` kullanılabilir.

## Kurulum

```bash
php database/seed.php
php -S 127.0.0.1:8080 -t public public/router.php
```

Tarayıcı: [http://127.0.0.1:8080](http://127.0.0.1:8080)

Yönetici paneli: `/admin/giris/`  
Varsayılan hesap seed çıktısında yazılır (`admin@tefeyyuz.com`).

## Yapılandırma

`config/app.php`

- `base_url` — kanonik adres ve sitemap
- `sqlite_path`, `log_path`
- `adsense.enabled` / `client` / `slots` / `ads_txt`
- `analytics.google_tag_id`, `verification.*`

## Paylaşımlı hosting

1. Tüm dosyaları yükleyin. Belge kökü **`public/`** olmalı (veya `public/` içeriğini site köküne kopyalayıp `index.php` içindeki `dirname(__DIR__)` yollarını buna göre ayarlayın).
2. `storage/` yazılabilir olsun (chmod 775).
3. `php database/seed.php` bir kez çalıştırın (SSH yoksa SQLite dosyasını yerelde üretip `storage/database/` altına yükleyin).
4. `config/app.php` içinde `debug => false` ve `base_url` değerini `https://tefeyyuz.com` yapın.
5. Apache için `public/.htaccess` hazırdır.

## Dizinler

```
app/bootstrap.php         Ön yüz yönlendiricisi ve bağımlılıklar
config/app.php            Ayarlar
database/schema.sql       SQLite şeması
database/seed.php         Hugo content/*.md aktarımı
public/                   Belge kökü (index.php, varlıklar)
src/Controller|Service|Repository|Dto|Support
templates/                Hugo görünümüne denk PHP şablonları
storage/database/         tefeyyuz.sqlite
storage/logs/app.log
content/                  Kaynak Markdown (seed)
```
