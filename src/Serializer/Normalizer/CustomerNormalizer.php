<?php

namespace App\Serializer\Normalizer;

use App\Service\DateFormatter;
use App\Service\MoneyFormatter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CustomerNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        protected ObjectNormalizer $normalizer,
        protected MoneyFormatter $moneyFormatter,
        protected DateFormatter $dateFormatter)
    {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);
        $data['total'] = $this->moneyFormatter->format($object->getTotal());
        $data['lastTransactionAt'] = $this->dateFormatter->format($object->getLastTransaction());
        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof \App\Entity\Customer;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            \App\Entity\Customer::class => true,
        ];
    }
}
