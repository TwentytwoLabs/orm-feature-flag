<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Bridge\Doctrine\Orm\Tests\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TwentytwoLabs\FeatureFlagBundle\Bridge\Doctrine\Orm\Storage\OrmStorage;
use TwentytwoLabs\FeatureFlagBundle\Model\FeatureInterface;

final class OrmStorageTest extends TestCase
{
    /** @var MockObject|ObjectRepository<FeatureInterface> */
    private ObjectRepository|MockObject $objectRepository;

    protected function setUp(): void
    {
        $this->objectRepository = $this->createMock(EntityRepository::class);
    }

    public function testShouldGetAllFeatures(): void
    {
        $this->objectRepository->expects($this->once())->method('findAll')->willReturn([]);

        $storage = $this->getStorage();
        $this->assertSame([], $storage->all());
    }

    public function testShouldGetOneFeatureButDontExist(): void
    {
        $this->objectRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'foo'])
            ->willReturn(null)
        ;

        $storage = $this->getStorage();
        $this->assertNull($storage->get('foo'));
    }

    public function testShouldGetOneFeature(): void
    {
        $feature = $this->createMock(FeatureInterface::class);

        $this->objectRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'foo'])
            ->willReturn($feature)
        ;

        $storage = $this->getStorage();
        $this->assertSame($feature, $storage->get('foo'));
    }

    private function getStorage(): OrmStorage
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('getRepository')->with(\stdClass::class)->willReturn($this->objectRepository);

        return new OrmStorage($em, ['class' => \stdClass::class, 'identifier' => 'name']);
    }
}
