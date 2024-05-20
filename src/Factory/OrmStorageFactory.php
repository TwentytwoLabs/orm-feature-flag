<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TwentytwoLabs\FeatureFlagBundle\Storage\OrmStorage;
use TwentytwoLabs\FeatureFlagBundle\Storage\StorageInterface;

final class OrmStorageFactory extends AbstractStorageFactory
{
    private ?EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em = null)
    {
        $this->em = $em;
    }

    public function createStorage(string $storageName, array $options = []): StorageInterface
    {
        if (null === $this->em) {
            throw new \LogicException('The "doctrine/orm" library must be installed.');
        }

        $this->validate($storageName, $options);

        return new OrmStorage($this->em, $options['class']);
    }

    protected function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('class')
            ->setAllowedTypes('class', ['string'])
        ;
    }
}
