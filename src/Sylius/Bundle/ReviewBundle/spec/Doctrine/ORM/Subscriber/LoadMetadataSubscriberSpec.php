<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ReviewBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class LoadMetadataSubscriberSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
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
    }

    function it_implements_event_subscriber(): void
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    function it_has_subscribed_events(): void
    {
        $this->getSubscribedEvents()->shouldReturn(['loadClassMetadata']);
    }

    function it_maps_proper_relations_for_review_model(
        ClassMetadataFactory $metadataFactory,
        ClassMetadata $classMetadataInfo,
        ClassMetadata $metadata,
        EntityManager $entityManager,
        LoadClassMetadataEventArgs $eventArguments
    ): void {
        $eventArguments->getClassMetadata()->willReturn($metadata);
        $eventArguments->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($metadataFactory);

        $classMetadataInfo->fieldMappings = ['id' => ['columnName' => 'id']];
        $metadataFactory->getMetadataFor('AcmeBundle\Entity\ReviewableModel')->willReturn($classMetadataInfo);
        $metadataFactory->getMetadataFor('AcmeBundle\Entity\ReviewerModel')->willReturn($classMetadataInfo);
        $metadata->getName()->willReturn('AcmeBundle\Entity\ReviewModel');

        $metadata->mapManyToOne([
            'fieldName' => 'reviewSubject',
            'targetEntity' => 'AcmeBundle\Entity\ReviewableModel',
            'inversedBy' => 'reviews',
            'joinColumns' => [[
                'name' => 'reviewable_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ])->shouldBeCalled();

        $metadata->mapManyToOne([
            'fieldName' => 'author',
            'targetEntity' => 'AcmeBundle\Entity\ReviewerModel',
            'joinColumns' => [[
                'name' => 'author_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
            'cascade' => ['persist'],
        ])->shouldBeCalled();

        $this->loadClassMetadata($eventArguments);
    }

    function it_maps_proper_relations_for_reviewable_model(
        ClassMetadataFactory $metadataFactory,
        ClassMetadata $metadata,
        EntityManager $entityManager,
        LoadClassMetadataEventArgs $eventArguments
    ): void {
        $eventArguments->getClassMetadata()->willReturn($metadata);
        $eventArguments->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($metadataFactory);
        $metadata->getName()->willReturn('AcmeBundle\Entity\ReviewableModel');

        $metadata->mapOneToMany([
            'fieldName' => 'reviews',
            'targetEntity' => 'AcmeBundle\Entity\ReviewModel',
            'mappedBy' => 'reviewSubject',
            'cascade' => ['all'],
        ])->shouldBeCalled();

        $this->loadClassMetadata($eventArguments);
    }

    function it_skips_mapping_configuration_if_metadata_name_is_not_different(
        ClassMetadataFactory $metadataFactory,
        ClassMetadata $metadata,
        EntityManager $entityManager,
        LoadClassMetadataEventArgs $eventArguments
    ): void {
        $this->beConstructedWith([
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

        $eventArguments->getClassMetadata()->willReturn($metadata);
        $eventArguments->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($metadataFactory);
        $metadata->getName()->willReturn('AcmeBundle\Entity\ReviewModel');

        $metadata->mapManyToOne(Argument::type('array'))->shouldNotBeCalled();
        $metadata->mapManyToOne(Argument::type('array'))->shouldNotBeCalled();

        $this->loadClassMetadata($eventArguments);
    }
}
