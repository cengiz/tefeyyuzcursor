<?php
declare(strict_types=1);

use Tefeyyuz\Repository\CategoryRepository;
use Tefeyyuz\Repository\PageRepository;
use Tefeyyuz\Repository\TagRepository;
use Tefeyyuz\Support\Database;
use Tefeyyuz\Support\Logger;
use Tefeyyuz\Support\Markdown;
use Tefeyyuz\Support\Session;
use Tefeyyuz\Support\Slug;
use Tefeyyuz\Support\View;

$root = dirname(__DIR__);
$config = require $root . '/config/app.php';
date_default_timezone_set((string) $config['timezone']);
mb_internal_encoding('UTF-8');

spl_autoload_register(static function (string $class) use ($root): void {
    $prefix = 'Tefeyyuz\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $rel = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file = $root . '/src/' . $rel . '.php';
    if (is_file($file)) {
        require $file;
    }
});
require $root . '/vendor/Parsedown.php';

$logger = new Logger((string) $config['log_path']);
$path = (string) $config['sqlite_path'];
foreach ([$path, $path . '-wal', $path . '-shm'] as $f) {
    if (is_file($f)) {
        unlink($f);
    }
}

$db = new Database($path, $logger);
$schema = file_get_contents($root . '/database/schema.sql');
if ($schema === false) {
    throw new RuntimeException('Şema okunamadı.');
}
$db->pdo()->exec($schema);

$session = new Session((string) $config['session_name']);
$view = new View($root . '/templates', $config, $session);
$pages = new PageRepository($db, new Markdown(), $view);
$categories = new CategoryRepository($db);
$tags = new TagRepository($db);

$email = 'admin@tefeyyuz.com';
$password = 'Tefeyyuz!2026';
$db->execute(
    'INSERT INTO users (email, password_hash, name, role, created_at) VALUES (:email, :password_hash, :name, :role, :created_at)',
    [
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'name' => 'Tefeyyüz Editörü',
        'role' => 'admin',
        'created_at' => date('c'),
    ]
);

$catIds = [];
$sort = 0;
foreach (glob($root . '/content/kategoriler/*/_index.md') ?: [] as $file) {
    $parsed = seed_front_matter((string) file_get_contents($file));
    $name = (string) ($parsed['meta']['title'] ?? basename(dirname($file)));
    $slug = Slug::make($name);
    $id = $categories->save(
        $name,
        $slug,
        (string) ($parsed['meta']['description'] ?? ''),
        (string) ($parsed['meta']['ikon'] ?? '📚')
    );
    $catIds[$name] = $id;
    $sort++;
}

$skip = ['_index.md', 'ara.md'];
$files = array_merge(
    glob($root . '/content/ansiklopedi/*.md') ?: [],
    glob($root . '/content/sorular/*.md') ?: [],
    glob($root . '/content/*.md') ?: []
);

$count = 0;
foreach ($files as $file) {
    $base = basename($file);
    if (in_array($base, $skip, true)) {
        continue;
    }
    $parsed = seed_front_matter((string) file_get_contents($file));
    $meta = $parsed['meta'];
    $body = trim($parsed['body']);
    $rel = str_replace('\\', '/', substr($file, strlen($root . '/content/')));
    $type = str_starts_with($rel, 'ansiklopedi/') ? 'article' : (str_starts_with($rel, 'sorular/') ? 'question' : 'static');
    $slug = (string) ($meta['slug'] ?? Slug::make((string) ($meta['title'] ?? pathinfo($base, PATHINFO_FILENAME))));
    $now = date('c');
    $published = seed_date($meta['date'] ?? null) ?: $now;
    $updated = seed_date($meta['lastmod'] ?? null) ?: $published;
    $faq = [];
    foreach ((array) ($meta['sss'] ?? []) as $row) {
        if (!is_array($row)) {
            continue;
        }
        $faq[] = ['soru' => (string) ($row['soru'] ?? ''), 'cevap' => (string) ($row['cevap'] ?? '')];
    }
    $sources = [];
    foreach ((array) ($meta['kaynaklar'] ?? []) as $src) {
        $sources[] = is_array($src) ? implode(' ', array_map('strval', $src)) : (string) $src;
    }
    $keywords = array_values(array_map('strval', (array) ($meta['keywords'] ?? [])));
    $data = [
        'type' => $type,
        'slug' => $slug,
        'title' => (string) ($meta['title'] ?? $slug),
        'seo_title' => (string) ($meta['seoTitle'] ?? ''),
        'description' => (string) ($meta['description'] ?? ''),
        'body_md' => $body,
        'excerpt' => '',
        'ozet' => (string) ($meta['ozet'] ?? ''),
        'author' => (string) ($meta['author'] ?? $config['author']),
        'asker' => (string) ($meta['soran'] ?? ''),
        'question_text' => (string) ($meta['soruMetni'] ?? ''),
        'short_answer' => (string) ($meta['kisaCevap'] ?? ''),
        'featured' => !empty($meta['oneCikan']) ? 1 : 0,
        'ads_enabled' => isset($meta['reklam']) && $meta['reklam'] === false ? 0 : 1,
        'toc_enabled' => isset($meta['toc']) && $meta['toc'] === false ? 0 : 1,
        'noindex' => !empty($meta['noindex']) ? 1 : 0,
        'show_date' => !empty($meta['gosterTarih']) ? 1 : 0,
        'image' => (string) ($meta['image'] ?? ''),
        'image_alt' => (string) ($meta['imageAlt'] ?? ''),
        'image_caption' => (string) ($meta['imageCaption'] ?? ''),
        'votes' => (int) ($meta['oy'] ?? 0),
        'keywords_json' => json_encode($keywords, JSON_UNESCAPED_UNICODE),
        'sources_json' => json_encode($sources, JSON_UNESCAPED_UNICODE),
        'faq_json' => json_encode($faq, JSON_UNESCAPED_UNICODE),
        'status' => !empty($meta['draft']) ? 'draft' : 'published',
        'published_at' => $published,
        'updated_at' => $updated,
    ];
    $pageCatIds = [];
    foreach ((array) ($meta['kategoriler'] ?? []) as $catName) {
        $name = (string) $catName;
        if (!isset($catIds[$name])) {
            $catIds[$name] = $categories->save($name, Slug::make($name), '', '📚');
        }
        $pageCatIds[] = $catIds[$name];
    }
    $tagIds = [];
    foreach ((array) ($meta['etiketler'] ?? []) as $tagName) {
        $tagIds[] = $tags->findOrCreate((string) $tagName);
    }
    $pages->save($data, $pageCatIds, $tagIds, null);
    $count++;
}

$logger->info('Veritabanı dolduruldu', ['sayfa' => $count, 'kategori' => count($catIds)]);
fwrite(STDOUT, "SQLite hazır: {$path}\n");
fwrite(STDOUT, "{$count} içerik, " . count($catIds) . " kategori aktarıldı.\n");
fwrite(STDOUT, "Yönetici: {$email} / {$password}\n");

function seed_date(mixed $value): string
{
    if (!is_string($value) || trim($value) === '') {
        return '';
    }
    $ts = strtotime($value);
    return $ts ? date('c', $ts) : $value;
}

/** @return array{meta: array<string, mixed>, body: string} */
function seed_front_matter(string $raw): array
{
    $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw) ?? $raw;
    if (preg_match('/\A---\r?\n(.*)\r?\n---\r?\n?(.*)\z/s', $raw, $m)) {
        return ['meta' => seed_yaml($m[1]), 'body' => $m[2]];
    }
    return ['meta' => [], 'body' => $raw];
}

/** @return array<string, mixed> */
function seed_yaml(string $src): array
{
    $lines = preg_split("/\r\n|\n|\r/", $src) ?: [];
    $root = [];
    $stack = [['indent' => -1, 'ref' => &$root]];
    $i = 0;
    $n = count($lines);
    while ($i < $n) {
        $line = $lines[$i];
        $i++;
        if (trim($line) === '' || str_starts_with(ltrim($line), '#')) {
            continue;
        }
        preg_match('/^(\s*)(.*)$/', $line, $m);
        $indent = strlen($m[1]);
        $content = $m[2];
        while (count($stack) > 1 && $indent <= $stack[array_key_last($stack)]['indent']) {
            array_pop($stack);
        }
        $cur = &$stack[array_key_last($stack)]['ref'];
        if (str_starts_with($content, '- ')) {
            $item = substr($content, 2);
            $looksLikeMap = $item === '' || (bool) preg_match('/^[\p{L}\w]+:\s/u', $item);
            if ($looksLikeMap) {
                $map = [];
                if ($item !== '' && str_contains($item, ':')) {
                    [$k, $v] = explode(':', $item, 2);
                    $map[trim($k)] = seed_yaml_value($v);
                }
                $cur[] = $map;
                $idx = array_key_last($cur);
                $stack[] = ['indent' => $indent, 'ref' => &$cur[$idx]];
            } else {
                $cur[] = seed_yaml_value($item);
            }
            unset($cur);
            continue;
        }
        if (!str_contains($content, ':')) {
            unset($cur);
            continue;
        }
        [$key, $rest] = explode(':', $content, 2);
        $key = trim($key);
        $rest = ltrim($rest);
        if ($rest === '' || $rest === '|' || $rest === '>') {
            $cur[$key] = [];
            $stack[] = ['indent' => $indent, 'ref' => &$cur[$key]];
        } else {
            $cur[$key] = seed_yaml_value($rest);
        }
        unset($cur);
    }
    return $root;
}

function seed_yaml_value(string $raw): mixed
{
    $raw = trim($raw);
    if ($raw === 'true' || $raw === 'yes') {
        return true;
    }
    if ($raw === 'false' || $raw === 'no') {
        return false;
    }
    if ($raw === 'null' || $raw === '~' || $raw === '') {
        return '';
    }
    if (preg_match('/^-?\d+$/', $raw)) {
        return (int) $raw;
    }
    if (str_starts_with($raw, '[') && str_ends_with($raw, ']')) {
        $inner = trim(substr($raw, 1, -1));
        if ($inner === '') {
            return [];
        }
        $parts = preg_split('/\s*,\s*/', $inner) ?: [];
        return array_map(static fn (string $p) => seed_yaml_value($p), $parts);
    }
    if ((str_starts_with($raw, '"') && str_ends_with($raw, '"')) || (str_starts_with($raw, "'") && str_ends_with($raw, "'"))) {
        return stripcslashes(substr($raw, 1, -1));
    }
    return $raw;
}
