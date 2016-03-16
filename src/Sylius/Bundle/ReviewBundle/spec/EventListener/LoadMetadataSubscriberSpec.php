<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReviewBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class LoadMetadataSubscriberSpec extends ObjectBehavior
{
    function let()
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

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\EventListener\LoadMetadataSubscriber');
    }

    function it_implements_event_subscriber()
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    function it_has_subscribed_events()
    {
        $this->getSubscribedEvents()->shouldReturn(['loadClassMetadata']);
    }

    function it_maps_proper_relations_for_review_model(
        ClassMetadataFactory $metadataFactory,
        ClassMetadataInfo $classMetadataInfo,
        ClassMetadataInfo $metadata,
        EntityManager $entityManager,
        LoadClassMetadataEventArgs $eventArguments
    ) {
        $eventArguments->getClassMetadata()->willReturn($metadata)->shouldBeCalled();
        $eventArguments->getEntityManager()->willReturn($entityManager)->shouldBeCalled();
        $entityManager->getMetadataFactory()->willReturn($metadataFactory)->shouldBeCalled();

        $classMetadataInfo->fieldMappings = ['id' => ['columnName' => 'id']];
        $metadataFactory->getMetadataFor('AcmeBundle\Entity\ReviewableModel')->willReturn($classMetadataInfo)->shouldBeCalled();
        $metadataFactory->getMetadataFor('AcmeBundle\Entity\ReviewerModel')->willReturn($classMetadataInfo)->shouldBeCalled();

        $metadata->getName()->willReturn('AcmeBundle\Entity\ReviewModel');

        $metadata->mapManyToOne([
            'fieldName' => 'reviewSubject',
            'targetEntity' => 'AcmeBundle\Entity\ReviewableModel',
            'inversedBy' => 'reviews',
            'joinColumns' => [
                [
                    'name' => 'reviewable_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'onDelete' => 'CASCADE',
                ],
            ],
        ])->shouldBeCalled();

        $metadata->mapManyToOne([
            'fieldName' => 'author',
            'targetEntity' => 'AcmeBundle\Entity\ReviewerModel',
            'joinColumn' => [
                'name' => 'author_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ],
            'cascade' => ['persist'],
        ])->shouldBeCalled();

        $this->loadClassMetadata($eventArguments);
    }

    function it_maps_proper_relations_for_reviewable_model(
        ClassMetadataFactory $metadataFactory,
        ClassMetadataInfo $metadata,
        EntityManager $entityManager,
        LoadClassMetadataEventArgs $eventArguments
    ) {
        $eventArguments->getClassMetadata()->willReturn($metadata)->shouldBeCalled();
        $eventArguments->getEntityManager()->willReturn($entityManager)->shouldBeCalled();
        $entityManager->getMetadataFactory()->willReturn($metadataFactory)->shouldBeCalled();

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
        ClassMetadataInfo $metadata,
        EntityManager $entityManager,
        LoadClassMetadataEventArgs $eventArguments
    ) {
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

        $eventArguments->getClassMetadata()->willReturn($metadata)->shouldBeCalled();
        $eventArguments->getEntityManager()->willReturn($entityManager)->shouldBeCalled();
        $entityManager->getMetadataFactory()->willReturn($metadataFactory)->shouldBeCalled();
        $metadata->getName()->willReturn('AcmeBundle\Entity\ReviewModel')->shouldBeCalled();

        $metadata->mapManyToOne(Argument::type('array'))->shouldNotBeCalled();
        $metadata->mapManyToOne(Argument::type('array'))->shouldNotBeCalled();

        $this->loadClassMetadata($eventArguments);
    }
}
