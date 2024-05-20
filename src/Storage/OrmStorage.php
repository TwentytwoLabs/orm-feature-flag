<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Bridge\Doctrine\Orm\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use TwentytwoLabs\FeatureFlagBundle\Model\FeatureInterface;
use TwentytwoLabs\FeatureFlagBundle\Storage\StorageInterface;

final class OrmStorage implements StorageInterface
{
    /** @var ObjectRepository<FeatureInterface> */
    private ObjectRepository $objectRepository;

    public function __construct(EntityManagerInterface $em, string $class)
    {
        $this->objectRepository = $em->getRepository($class);
    }

    public function all(): array
    {
        return $this->objectRepository->findAll();
    }

    public function get(string $key): ?FeatureInterface
    {
        return $this->objectRepository->findOneBy(['name' => $key]);
    }
}
