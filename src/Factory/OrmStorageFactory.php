<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Bridge\Doctrine\Orm\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use TwentytwoLabs\FeatureFlagBundle\Bridge\Doctrine\Orm\Storage\OrmStorage;
use TwentytwoLabs\FeatureFlagBundle\Factory\AbstractStorageFactory;
use TwentytwoLabs\FeatureFlagBundle\Storage\StorageInterface;

final class OrmStorageFactory extends AbstractStorageFactory
{
    private NormalizerInterface $normalizer;
    private DenormalizerInterface $denormalizer;
    private ?EntityManagerInterface $em;

    public function __construct(
        NormalizerInterface $normalizer,
        DenormalizerInterface $denormalizer,
        EntityManagerInterface $em = null,
    ) {
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
        $this->em = $em;
    }

    public function createStorage(string $storageName, array $options = []): StorageInterface
    {
        if (null === $this->em) {
            throw new \LogicException('The "doctrine/orm" library must be installed.');
        }

        $options = $this->validate($storageName, $options);

        return new OrmStorage($this->normalizer, $this->denormalizer, $this->em, $options);
    }

    protected function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('class')
            ->setDefault('identifier', 'key')
            ->addAllowedTypes('class', ['string'])
            ->addAllowedTypes('identifier', ['string'])
        ;
    }
}
