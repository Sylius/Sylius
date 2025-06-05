<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\AttributeBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AttributeBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber;

final class LoadMetadataSubscriberTest extends TestCase
{
    private LoadMetadataSubscriber $loadMetadataSubscriber;

    private LoadClassMetadataEventArgs&MockObject $eventArgs;

    private ClassMetadata&MockObject $metadata;

    private EntityManager&MockObject $entityManager;

    private ClassMetadataFactory&MockObject $classMetadataFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMetadataSubscriber = new LoadMetadataSubscriber([
            'product' => [
                'subject' => 'Some\App\Product\Entity\Product',
                'attribute' => [
                    'classes' => [
                        'model' => 'Some\App\Product\Entity\Attribute',
                    ],
                ],
                'attribute_value' => [
                    'classes' => [
                        'model' => 'Some\App\Product\Entity\AttributeValue',
                    ],
                ],
            ],
        ]);
        $this->eventArgs = $this->createMock(LoadClassMetadataEventArgs::class);
        $this->metadata = $this->createMock(ClassMetadata::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->classMetadataFactory = $this->createMock(ClassMetadataFactory::class);
    }

    public function testADoctrineEventSubscriber(): void
    {
        self::assertInstanceOf(EventSubscriber::class, $this->loadMetadataSubscriber);
    }

    public function testSubscribesLoadClassMetadataEvent(): void
    {
        self::assertSame(['loadClassMetadata'], $this->loadMetadataSubscriber->getSubscribedEvents());
    }

    public function testMapsManyToOneAssociationsFromAttributeValueModelToSubjectModelAndAttributeModel(): void
    {
        $this->eventArgs->expects(self::once())
            ->method('getClassMetadata')
            ->willReturn($this->metadata);

        $this->eventArgs->expects(self::once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($this->classMetadataFactory);

        $this->metadata->fieldMappings = [
            'id' => [
                'columnName' => 'id',
                'type' => 'integer',
                'id' => true,
                'nullable' => false,
                'fieldName' => 'id',
            ],
        ];

        $this->classMetadataFactory->expects(self::exactly(2))
            ->method('getMetadataFor')
            ->willReturnMap([
                ['Some\App\Product\Entity\Product', $this->metadata],
                ['Some\App\Product\Entity\Attribute', $this->metadata],
            ]);

        $this->metadata->expects(self::once())
            ->method('getName')
            ->willReturn('Some\App\Product\Entity\AttributeValue');

        $this->metadata->expects(self::exactly(2))
            ->method('hasAssociation')
            ->willReturnMap([
                ['subject', false],
                ['attribute', false],
            ]);

        $subjectMapping = [
            'fieldName' => 'subject',
            'targetEntity' => 'Some\App\Product\Entity\Product',
            'inversedBy' => 'attributes',
            'joinColumns' => [[
                'name' => 'product_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];

        $attributeMapping = [
            'fieldName' => 'attribute',
            'targetEntity' => 'Some\App\Product\Entity\Attribute',
            'joinColumns' => [[
                'name' => 'attribute_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
            ]],
        ];

        $this->metadata->expects(self::exactly(2))
            ->method('mapManyToOne')
            ->willReturnCallback(function ($mapping) use ($subjectMapping, $attributeMapping) {
                static $callCount = 0;
                ++$callCount;

                if ($callCount === 1) {
                    self::assertEquals($subjectMapping, $mapping);
                } elseif ($callCount === 2) {
                    self::assertEquals($attributeMapping, $mapping);
                }
            });

        $this->loadMetadataSubscriber->loadClassMetadata($this->eventArgs);
    }

    public function testDoesNotMapRelationsForAttributeValueModelIfTheRelationsAlreadyExist(): void
    {
        $this->eventArgs->expects(self::once())
            ->method('getClassMetadata')
            ->willReturn($this->metadata);

        $this->eventArgs->expects(self::once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($this->classMetadataFactory);

        $this->metadata->expects(self::once())
            ->method('getName')
            ->willReturn('Some\App\Product\Entity\AttributeValue');

        $this->metadata->expects(self::exactly(2))
            ->method('hasAssociation')
            ->willReturnMap([['subject', true], ['attribute', true]]);

        $this->metadata->expects(self::never())->method('mapManyToOne');

        $this->loadMetadataSubscriber->loadClassMetadata($this->eventArgs);
    }

    public function testDoesNotAddAManyToOneMappingIfTheClassIsNotAConfiguredAttributeValueModel(): void
    {
        $this->eventArgs->expects(self::once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($this->classMetadataFactory);

        $this->eventArgs->expects(self::once())
            ->method('getClassMetadata')
            ->willReturn($this->metadata);

        $this->metadata->expects(self::once())
            ->method('getName')
            ->willReturn('KeepMoving\ThisClass\DoesNot\Concern\You');

        $this->metadata->expects(self::never())->method('mapManyToOne');

        $this->loadMetadataSubscriber->loadClassMetadata($this->eventArgs);
    }

    public function testDoesNotAddValuesOneToManyMappingIfTheClassIsNotAConfiguredAttributeModel(): void
    {
        $this->eventArgs->expects(self::once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($this->classMetadataFactory);

        $this->eventArgs->expects(self::once())->method('getClassMetadata')->willReturn($this->metadata);

        $this->metadata->expects(self::once())
            ->method('getName')
            ->willReturn('KeepMoving\ThisClass\DoesNot\Concern\You');

        $this->metadata->expects(self::never())->method('mapOneToMany');

        $this->loadMetadataSubscriber->loadClassMetadata($this->eventArgs);
    }
}
