<?php
declare(strict_types=1);

namespace Tefeyyuz\Service;

use Tefeyyuz\Repository\SubmissionRepository;
use Tefeyyuz\Support\Logger;

final class SubmissionService
{
    public function __construct(
        private readonly SubmissionRepository $submissions,
        private readonly Logger $logger,
    ) {
    }

    public function submit(array $post): string
    {
        try {
            if (!empty($post['website'])) {
                return 'Teşekkürler! Sorunuz bize ulaştı.';
            }
            $title = trim((string) ($post['soru'] ?? ''));
            if ($title === '' || mb_strlen($title) > 200) {
                throw new \InvalidArgumentException('Geçerli bir soru yazın.');
            }
            if (empty($post['kvkk'])) {
                throw new \InvalidArgumentException('Gizlilik politikasını kabul etmelisiniz.');
            }
            $this->submissions->create(
                $title,
                trim((string) ($post['detay'] ?? '')),
                trim((string) ($post['kategori'] ?? '')),
                trim((string) ($post['email'] ?? '')),
                trim((string) ($post['ad'] ?? ''))
            );
            $this->logger->info('Yeni soru gönderildi', ['baslik' => $title]);
            return 'Teşekkürler! Sorunuz bize ulaştı. Editörlerimiz inceledikten sonra kaynaklı bir cevapla yayınlayacağız.';
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning('Soru gönderimi reddedildi', ['hata' => $e->getMessage()]);
            throw $e;
        } catch (\Throwable $e) {
            $this->logger->error('Soru kaydedilemedi', ['hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function pending(): array
    {
        try {
            return $this->submissions->pending();
        } catch (\Throwable $e) {
            $this->logger->error('Gelen sorular yüklenemedi', ['hata' => $e->getMessage()]);
            throw $e;
        }
    }

    public function reject(int $id): void
    {
        try {
            $this->submissions->delete($id);
        } catch (\Throwable $e) {
            $this->logger->error('Gelen soru silinemedi', ['id' => $id, 'hata' => $e->getMessage()]);
            throw $e;
        }
    }
}
