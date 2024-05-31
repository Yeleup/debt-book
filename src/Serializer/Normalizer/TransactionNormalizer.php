<?php

namespace App\Serializer\Normalizer;

use App\Service\DateFormatter;
use App\Service\MoneyFormatter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TransactionNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private ObjectNormalizer $normalizer,
        private MoneyFormatter $moneyFormatter,
        private DateFormatter $dateFormatter)
    {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);
        $data['amount'] = $this->moneyFormatter->format($object->getAmount());
        $data['createdAt'] = $this->dateFormatter->format($object->getCreatedAt());
        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof \App\Entity\Transaction;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            \App\Entity\Transaction::class => true,
        ];
    }
}
