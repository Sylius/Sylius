<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class LoadMetadataSubscriberSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
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
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\EventListener\LoadMetadataSubscriber');
    }

    function it_is_a_Doctrine_event_subscriber()
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    function it_subscribes_to_loadClassMetadata_events_dispatched_by_Doctrine()
    {
        $this->getSubscribedEvents()->shouldReturn(['loadClassMetadata']);
    }

    function it_does_not_add_a_many_to_one_mapping_if_the_class_is_not_a_configured_attribute_value_model(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadataInfo $metadata,
        EntityManager $entityManager,
        ClassMetadataFactory $classMetadataFactory
    ) {
        $eventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($classMetadataFactory);

        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('KeepMoving\ThisClass\DoesNot\Concern\You');

        $metadata->mapManyToOne(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_maps_many_to_one_associations_from_the_attribute_value_model_to_the_subject_model_and_the_attribute_model(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadataInfo $metadata,
        EntityManager $entityManager,
        ClassMetadataFactory $classMetadataFactory,
        ClassMetadataInfo $classMetadataInfo
    ) {
        $eventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($classMetadataFactory);
        $classMetadataInfo->fieldMappings = [
            'id' => [
                'columnName' => 'id',
            ],
        ];
        $classMetadataFactory->getMetadataFor('Some\App\Product\Entity\Product')->willReturn($classMetadataInfo);
        $classMetadataFactory->getMetadataFor('Some\App\Product\Entity\Attribute')->willReturn($classMetadataInfo);

        $eventArgs->getClassMetadata()->willReturn($metadata);
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('Some\App\Product\Entity\AttributeValue');

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
            'inversedBy' => 'values',
            'targetEntity' => 'Some\App\Product\Entity\Attribute',
            'joinColumns' => [[
                'name' => 'attribute_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];

        $metadata->mapManyToOne($subjectMapping)->shouldBeCalled();
        $metadata->mapManyToOne($attributeMapping)->shouldBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_does_not_add_values_one_to_many_mapping_if_the_class_is_not_a_configured_attribute_model(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadataInfo $metadata,
        EntityManager $entityManager,
        ClassMetadataFactory $classMetadataFactory
    ) {
        $eventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($classMetadataFactory);

        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('KeepMoving\ThisClass\DoesNot\Concern\You');

        $metadata->mapOneToMany(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_maps_values_one_to_many_association_from_the_attribute_model_to_the_attribute_value_model(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadataInfo $metadata,
        EntityManager $entityManager,
        ClassMetadataFactory $classMetadataFactory
    ) {
        $eventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($classMetadataFactory);

        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('Some\App\Product\Entity\Attribute');

        $valuesMapping = [
            'fieldName' => 'values',
            'targetEntity' => 'Some\App\Product\Entity\AttributeValue',
            'mappedBy' => 'attribute',
        ];

        $metadata->mapOneToMany($valuesMapping)->shouldBeCalled();

        $this->loadClassMetadata($eventArgs);
    }
}
