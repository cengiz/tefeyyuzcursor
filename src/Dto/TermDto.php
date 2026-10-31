<?php
declare(strict_types=1);

namespace Tefeyyuz\Dto;

final class TermDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $description = '',
        public readonly string $icon = '',
        public readonly int $count = 0,
        public readonly string $url = '',
    ) {
    }
}
