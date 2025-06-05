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

namespace Tests\Sylius\Bundle\ReviewBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ReviewBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber;

final class LoadMetadataSubscriberTest extends TestCase
{
    private LoadMetadataSubscriber $loadMetadataSubscriber;

    /** @var ClassMetadataFactory&MockObject */
    private ClassMetadataFactory $metadataFactory;

    /** @var ClassMetadata&MockObject */
    private ClassMetadata $metadata;

    /** @var EntityManager&MockObject */
    private EntityManager $entityManager;

    /** @var LoadClassMetadataEventArgs&MockObject */
    private LoadClassMetadataEventArgs $eventArguments;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMetadataSubscriber = new LoadMetadataSubscriber([
            'reviewable' => [
                'subject' => 'AcmeBundle\Entity\ReviewableModel',
                'review' => [
                    'classes' => [
                        'model' => 'AcmeBundle\Entity\ReviewModel',
                    ],
                ],
                'reviewer' => [
                    'classes' => [
                        'model' => 'AcmeBundle\Entity\ReviewerModel',
                    ],
                ],
            ],
        ]);
        $this->metadataFactory = $this->createMock(ClassMetadataFactory::class);
        $this->metadata = $this->createMock(ClassMetadata::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->eventArguments = $this->createMock(LoadClassMetadataEventArgs::class);
    }

    public function testImplementsEventSubscriber(): void
    {
        self::assertInstanceOf(EventSubscriber::class, $this->loadMetadataSubscriber);
    }

    public function testHasSubscribedEvents(): void
    {
        self::assertSame(['loadClassMetadata'], $this->loadMetadataSubscriber->getSubscribedEvents());
    }

    public function testMapsProperRelationsForReviewModel(): void
    {
        $classMetadataInfo = $this->createMock(ClassMetadata::class);

        $this->eventArguments->expects(self::once())
            ->method('getClassMetadata')
            ->willReturn($this->metadata);

        $this->eventArguments->expects(self::once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($this->metadataFactory);

        $classMetadataInfo->fieldMappings = [
            'id' => [
                'columnName' => 'id',
                'type' => 'integer',
                'fieldName' => 'id',
            ],
        ];

        $this->metadataFactory->expects(self::exactly(2))
            ->method('getMetadataFor')
            ->willReturnCallback(function ($className) use ($classMetadataInfo) {
                if (in_array($className, [
                    'AcmeBundle\Entity\ReviewableModel',
                    'AcmeBundle\Entity\ReviewerModel',
                ])) {
                    return $classMetadataInfo;
                }
                self::fail('Unexpected class name: ' . $className);
            });

        $this->metadata->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn('AcmeBundle\Entity\ReviewModel');

        $this->metadata->expects(self::exactly(2))
            ->method('hasAssociation')
            ->willReturnCallback(function ($field) {
                if (!in_array($field, ['reviewSubject', 'author'])) {
                    self::fail('Unexpected field name: ' . $field);
                }

                return false;
            });

        $expectedCalls = [
            [
                'fieldName' => 'reviewSubject',
                'targetEntity' => 'AcmeBundle\Entity\ReviewableModel',
                'inversedBy' => 'reviews',
                'joinColumns' => [[
                    'name' => 'reviewable_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'onDelete' => 'CASCADE',
                ]],
            ],
            [
                'fieldName' => 'author',
                'targetEntity' => 'AcmeBundle\Entity\ReviewerModel',
                'joinColumns' => [[
                    'name' => 'author_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'onDelete' => 'CASCADE',
                ]],
                'cascade' => ['persist'],
            ],
        ];

        $callCount = 0;
        $this->metadata->expects(self::exactly(2))
            ->method('mapManyToOne')
            ->willReturnCallback(function ($params) use (&$callCount, $expectedCalls) {
                self::assertEquals($expectedCalls[$callCount], $params);
                ++$callCount;
            });

        $this->loadMetadataSubscriber->loadClassMetadata($this->eventArguments);
    }

    public function testDoesNotMapRelationForReviewModelIfTheRelationAlreadyExists(): void
    {
        $this->eventArguments->expects(self::once())
            ->method('getClassMetadata')
            ->willReturn($this->metadata);

        $this->eventArguments->expects(self::once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($this->metadataFactory);

        $this->metadata->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn('AcmeBundle\Entity\ReviewModel');

        $this->metadata->expects(self::exactly(2))
            ->method('hasAssociation')
            ->willReturnCallback(function ($field) {
                if (!in_array($field, ['reviewSubject', 'author'])) {
                    self::fail('Unexpected field name: ' . $field);
                }

                return true;
            });

        $this->metadata->expects(self::never())->method('mapManyToOne');

        $this->loadMetadataSubscriber->loadClassMetadata($this->eventArguments);
    }

    public function testMapsProperRelationsForReviewableModel(): void
    {
        $this->eventArguments->expects(self::once())
            ->method('getClassMetadata')
            ->willReturn($this->metadata);

        $this->eventArguments->expects(self::once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($this->metadataFactory);

        $this->metadata->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn('AcmeBundle\Entity\ReviewableModel');

        $this->metadata->expects(self::once())
            ->method('hasAssociation')
            ->with('reviews')
            ->willReturn(false);

        $this->metadata->expects(self::once())
            ->method('mapOneToMany')
            ->with([
                'fieldName' => 'reviews',
                'targetEntity' => 'AcmeBundle\Entity\ReviewModel',
                'mappedBy' => 'reviewSubject',
                'cascade' => ['all'],
            ]);

        $this->loadMetadataSubscriber->loadClassMetadata($this->eventArguments);
    }

    public function testDoesNotMapRelationsForReviewableModelIfTheRelationAlreadyExists(): void
    {
        $this->eventArguments->expects(self::once())
            ->method('getClassMetadata')
            ->willReturn($this->metadata);

        $this->eventArguments->expects(self::once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($this->metadataFactory);

        $this->metadata->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn('AcmeBundle\Entity\ReviewableModel');

        $this->metadata->expects(self::once())
            ->method('hasAssociation')
            ->with('reviews')
            ->willReturn(true);

        $this->metadata->expects(self::never())->method('mapOneToMany');

        $this->loadMetadataSubscriber->loadClassMetadata($this->eventArguments);
    }

    public function testSkipsMappingConfigurationIfMetadataNameIsDifferent(): void
    {
        $this->loadMetadataSubscriber = new LoadMetadataSubscriber([
            'reviewable' => [
                'subject' => 'AcmeBundle\Entity\ReviewableModel',
                'review' => [
                    'classes' => [
                        'model' => 'AcmeBundle\Entity\BadReviewModel',
                    ],
                ],
                'reviewer' => [
                    'classes' => [
                        'model' => 'AcmeBundle\Entity\ReviewerModel',
                    ],
                ],
            ],
        ]);

        $this->eventArguments->expects(self::once())
            ->method('getClassMetadata')
            ->willReturn($this->metadata);

        $this->eventArguments->expects(self::once())
            ->method('getEntityManager')
            ->willReturn($this->entityManager);

        $this->entityManager->expects(self::once())
            ->method('getMetadataFactory')
            ->willReturn($this->metadataFactory);

        $this->metadata->expects(self::atLeastOnce())
            ->method('getName')
            ->willReturn('AcmeBundle\Entity\ReviewModel');
        $this->metadata->expects(self::never())
            ->method('mapManyToOne');

        $this->loadMetadataSubscriber->loadClassMetadata($this->eventArguments);
    }
}
