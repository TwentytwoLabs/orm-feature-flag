<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Bridge\Doctrine\Orm\Tests\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use TwentytwoLabs\FeatureFlagBundle\Bridge\Doctrine\Orm\Storage\OrmStorage;
use TwentytwoLabs\FeatureFlagBundle\Model\FeatureInterface;

final class OrmStorageTest extends TestCase
{
    private NormalizerInterface $normalizer;
    private DenormalizerInterface $denormalizer;
    /** @var MockObject|ObjectRepository<FeatureInterface> */
    private ObjectRepository|MockObject $objectRepository;

    protected function setUp(): void
    {
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->denormalizer = $this->createMock(DenormalizerInterface::class);
        $this->objectRepository = $this->createMock(EntityRepository::class);
    }

    public function testShouldNotGetAllFeaturesBecauseItIsEmpty(): void
    {
        $this->normalizer->expects($this->never())->method('normalize');
        $this->denormalizer->expects($this->never())->method('denormalize');

        $this->objectRepository->expects($this->once())->method('findAll')->willReturn([]);

        $storage = $this->getStorage();
        $this->assertSame([], $storage->all());
    }

    public function testShouldGetAllFeaturesWithInterface(): void
    {
        $feature = $this->createMock(FeatureInterface::class);

        $this->normalizer->expects($this->never())->method('normalize');
        $this->denormalizer->expects($this->never())->method('denormalize');

        $this->objectRepository->expects($this->once())->method('findAll')->willReturn([$feature]);

        $storage = $this->getStorage();
        $this->assertSame([$feature], $storage->all());
    }

    public function testShouldGetAllFeaturesWithoutInterface(): void
    {
        $feature = $this->createMock(\stdClass::class);
        $f = $this->createMock(FeatureInterface::class);

        $this->normalizer->expects($this->once())->method('normalize')->with($feature)->willReturn(['foo' => 'bar']);
        $this->denormalizer->expects($this->once())->method('denormalize')->with(['foo' => 'bar'])->willReturn($f);

        $this->objectRepository->expects($this->once())->method('findAll')->willReturn([$feature]);

        $storage = $this->getStorage();
        $this->assertSame([$f], $storage->all());
    }

    public function testShouldGetOneFeatureButDontExist(): void
    {
        $this->normalizer->expects($this->never())->method('normalize');
        $this->denormalizer->expects($this->never())->method('denormalize');

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

        $this->normalizer->expects($this->never())->method('normalize');
        $this->denormalizer->expects($this->never())->method('denormalize');

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

        return new OrmStorage(
            $this->normalizer,
            $this->denormalizer,
            $em,
            ['class' => \stdClass::class, 'identifier' => 'name']
        );
    }
}
