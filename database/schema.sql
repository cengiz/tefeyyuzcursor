PRAGMA journal_mode = WAL;
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL,
    name TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'admin',
    created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT NOT NULL DEFAULT '',
    icon TEXT NOT NULL DEFAULT '📚',
    sort_order INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS pages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type TEXT NOT NULL,
    slug TEXT NOT NULL,
    title TEXT NOT NULL,
    seo_title TEXT NOT NULL DEFAULT '',
    description TEXT NOT NULL DEFAULT '',
    body_md TEXT NOT NULL DEFAULT '',
    excerpt TEXT NOT NULL DEFAULT '',
    ozet TEXT NOT NULL DEFAULT '',
    author TEXT NOT NULL DEFAULT '',
    asker TEXT NOT NULL DEFAULT '',
    question_text TEXT NOT NULL DEFAULT '',
    short_answer TEXT NOT NULL DEFAULT '',
    featured INTEGER NOT NULL DEFAULT 0,
    ads_enabled INTEGER NOT NULL DEFAULT 1,
    toc_enabled INTEGER NOT NULL DEFAULT 1,
    noindex INTEGER NOT NULL DEFAULT 0,
    show_date INTEGER NOT NULL DEFAULT 0,
    image TEXT NOT NULL DEFAULT '',
    image_alt TEXT NOT NULL DEFAULT '',
    image_caption TEXT NOT NULL DEFAULT '',
    votes INTEGER NOT NULL DEFAULT 0,
    keywords_json TEXT NOT NULL DEFAULT '[]',
    sources_json TEXT NOT NULL DEFAULT '[]',
    faq_json TEXT NOT NULL DEFAULT '[]',
    status TEXT NOT NULL DEFAULT 'published',
    published_at TEXT NOT NULL,
    updated_at TEXT NOT NULL,
    UNIQUE(type, slug)
);

CREATE INDEX IF NOT EXISTS idx_pages_type_status_date ON pages(type, status, published_at DESC);
CREATE INDEX IF NOT EXISTS idx_pages_featured ON pages(featured, status);

CREATE TABLE IF NOT EXISTS page_categories (
    page_id INTEGER NOT NULL,
    category_id INTEGER NOT NULL,
    PRIMARY KEY (page_id, category_id),
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS page_tags (
    page_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    PRIMARY KEY (page_id, tag_id),
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS submissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    detail TEXT NOT NULL DEFAULT '',
    category TEXT NOT NULL DEFAULT '',
    email TEXT NOT NULL DEFAULT '',
    display_name TEXT NOT NULL DEFAULT '',
    status TEXT NOT NULL DEFAULT 'pending',
    created_at TEXT NOT NULL
);

CREATE VIRTUAL TABLE IF NOT EXISTS pages_fts USING fts5(
    title,
    body,
    extra,
    tokenize='unicode61'
);
