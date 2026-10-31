<?php
declare(strict_types=1);

namespace Tefeyyuz\Dto;

final class SubmissionDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $detail,
        public readonly string $category,
        public readonly string $email,
        public readonly string $displayName,
        public readonly string $status,
        public readonly string $createdAt,
    ) {
    }
}
