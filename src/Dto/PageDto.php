<?php
declare(strict_types=1);

namespace Tefeyyuz\Dto;

final class PageDto
{
    /**
     * @param list<TermDto> $categories
     * @param list<TermDto> $tags
     * @param list<string> $keywords
     * @param list<string> $sources
     * @param list<array{soru:string,cevap:string}> $faq
     */
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly string $slug,
        public readonly string $title,
        public readonly string $seoTitle,
        public readonly string $description,
        public readonly string $bodyMd,
        public readonly string $html,
        public readonly string $excerpt,
        public readonly string $ozet,
        public readonly string $author,
        public readonly string $asker,
        public readonly string $questionText,
        public readonly string $shortAnswer,
        public readonly bool $featured,
        public readonly bool $adsEnabled,
        public readonly bool $tocEnabled,
        public readonly bool $noindex,
        public readonly bool $showDate,
        public readonly string $image,
        public readonly string $imageAlt,
        public readonly string $imageCaption,
        public readonly int $votes,
        public readonly array $keywords,
        public readonly array $sources,
        public readonly array $faq,
        public readonly string $status,
        public readonly string $publishedAt,
        public readonly string $updatedAt,
        public readonly string $url,
        public readonly int $readingTime,
        public readonly string $tocHtml,
        public readonly array $categories,
        public readonly array $tags,
        public readonly ?self $prev = null,
        public readonly ?self $next = null,
    ) {
    }

    public function isQuestion(): bool
    {
        return $this->type === 'question';
    }

    public function permalinkPath(): string
    {
        return match ($this->type) {
            'article' => '/ansiklopedi/' . $this->slug . '/',
            'question' => '/soru/' . $this->slug . '/',
            default => '/' . $this->slug . '/',
        };
    }
}
