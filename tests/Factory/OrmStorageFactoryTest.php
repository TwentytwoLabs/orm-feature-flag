<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Bridge\Doctrine\Orm\Tests\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use TwentytwoLabs\FeatureFlagBundle\Bridge\Doctrine\Orm\Factory\OrmStorageFactory;
use TwentytwoLabs\FeatureFlagBundle\Bridge\Doctrine\Orm\Storage\OrmStorage;
use TwentytwoLabs\FeatureFlagBundle\Exception\ConfigurationException;
use TwentytwoLabs\FeatureFlagBundle\Model\Feature;
use PHPUnit\Framework\TestCase;

final class OrmStorageFactoryTest extends TestCase
{
    private EntityManagerInterface|MockObject $em;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
    }

    public function testShouldNotCreateStorageBecauseEntityManagerIsNotInstalled(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The "doctrine/orm" library must be installed.');

        $factory = new OrmStorageFactory();
        $factory->createStorage('default');
    }

    public function testShouldNotCreateStorageBecauseClassIsMissing(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Error while configure storage default. Verify your configuration at "twenty-two-labs.feature-flags.storages.default.options". The required option "class" is missing.');

        $factory = $this->getFactory();
        $factory->createStorage('default', ['identifier' => 'slug']);
    }

    public function testShouldNotCreateStorageBecauseClassIsAnArray(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Error while configure storage default. Verify your configuration at "twenty-two-labs.feature-flags.storages.default.options". The option "class" with value array is expected to be of type "string", but is of type "array".');

        $factory = $this->getFactory();
        $storage = $factory->createStorage('default', ['class' => [], 'identifier' => 'slug']);
        $this->assertInstanceOf(OrmStorage::class, $storage);
    }

    public function testShouldNotCreateStorageBecauseIdentifierIsAnArray(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Error while configure storage default. Verify your configuration at "twenty-two-labs.feature-flags.storages.default.options". The option "identifier" with value array is expected to be of type "string", but is of type "array"');

        $factory = $this->getFactory();
        $storage = $factory->createStorage('default', ['class' => Feature::class, 'identifier' => []]);
        $this->assertInstanceOf(OrmStorage::class, $storage);
    }

    public function testShouldCreateStorage(): void
    {
        $objectRepository = $this->createMock(EntityRepository::class);
        $this->em->expects($this->once())->method('getRepository')->with(Feature::class)->willReturn($objectRepository);

        $factory = $this->getFactory();
        $storage = $factory->createStorage('default', ['class' => Feature::class, 'identifier' => 'slug']);
        $this->assertInstanceOf(OrmStorage::class, $storage);
    }

    private function getFactory(): OrmStorageFactory
    {
        return new OrmStorageFactory($this->em);
    }
}
