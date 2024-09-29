<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Bridge\Doctrine\Orm\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use TwentytwoLabs\FeatureFlagBundle\Model\FeatureInterface;
use TwentytwoLabs\FeatureFlagBundle\Storage\StorageInterface;

final class OrmStorage implements StorageInterface
{
    private NormalizerInterface $normalizer;
    private DenormalizerInterface $denormalizer;
    /** @var ObjectRepository<object> */
    private ObjectRepository $objectRepository;
    /** @var array<string, mixed> */
    private array $options;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        NormalizerInterface $normalizer,
        DenormalizerInterface $denormalizer,
        EntityManagerInterface $em,
        array $options,
    ) {
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
        $this->objectRepository = $em->getRepository($options['class']);
        $this->options = $options;
    }

    public function all(): array
    {
        return array_map(fn (object $feature) => $this->transform($feature), $this->objectRepository->findAll());
    }

    public function get(string $key): ?FeatureInterface
    {
        $feature = $this->objectRepository->findOneBy([$this->options['identifier'] => $key]);
        if (null === $feature) {
            return null;
        }

        return $this->transform($feature);
    }

    private function transform(object $feature): FeatureInterface
    {
        if ($feature instanceof FeatureInterface) {
            return $feature;
        }

        return $this->denormalizer->denormalize($this->normalizer->normalize($feature), FeatureInterface::class);
    }
}
