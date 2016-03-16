<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ArchetypeBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
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
                'attribute' => 'Some\App\Product\Entity\Attribute',
                'option' => 'Some\App\Product\Entity\Option',
                'archetype' => [
                    'classes' => [
                        'model' => 'Some\App\Product\Entity\Archetype',
                    ],
                ],
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ArchetypeBundle\EventListener\LoadMetadataSubscriber');
    }

    function it_is_a_Doctrine_event_subscriber()
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    function it_subscribes_to_loadClassMetadata_events_dispatched_by_Doctrine()
    {
        $this->getSubscribedEvents()->shouldReturn(['loadClassMetadata']);
    }

    function it_does_not_add_mapping_if_the_class_is_not_configured_to_be_an_archetype(LoadClassMetadataEventArgs $eventArgs, ClassMetadataInfo $metadata)
    {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('KeepMoving\ThisClass\DoesNot\Concern\You');

        $metadata->mapManyToMany(Argument::any())->shouldNotBeCalled();
        $metadata->mapOneToMany(Argument::any())->shouldNotBeCalled();
        $metadata->mapManyToOne(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_maps_attributes_and_options_to_archetypes(LoadClassMetadataEventArgs $eventArgs, ClassMetadataInfo $metadata)
    {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('Some\App\Product\Entity\Archetype');

        $attributeMapping = [
            'fieldName' => 'attributes',
            'type' => ClassMetadataInfo::MANY_TO_MANY,
            'targetEntity' => 'Some\App\Product\Entity\Attribute',
            'joinTable' => [
                'name' => 'sylius_product_archetype_attribute',
                'joinColumns' => [[
                    'name' => 'archetype_id', // or `product_archetype_id` ?
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'unique' => false,
                    'onDelete' => 'CASCADE',
                ]],
                'inverseJoinColumns' => [[
                    'name' => 'attribute_id', // or `product_attribute_id` ?
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'unique' => false,
                    'onDelete' => 'CASCADE',
                ]],
            ],
        ];

        $optionMapping = [
            'fieldName' => 'options',
            'type' => ClassMetadataInfo::MANY_TO_MANY,
            'targetEntity' => 'Some\App\Product\Entity\Option',
            'joinTable' => [
                'name' => 'sylius_product_archetype_option',
                'joinColumns' => [[
                    'name' => 'product_archetype_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'unique' => false,
                    'onDelete' => 'CASCADE',
                ]],
                'inverseJoinColumns' => [[
                    'name' => 'option_id',
                    'referencedColumnName' => 'id',
                    'nullable' => false,
                    'unique' => false,
                    'onDelete' => 'CASCADE',
                ]],
            ],
        ];

        $parentMapping = [
            'fieldName' => 'parent',
            'type' => ClassMetadataInfo::MANY_TO_ONE,
            'targetEntity' => 'Some\App\Product\Entity\Archetype',
            'joinColumn' => [
                'name' => 'parent_id',
                'referencedColumnName' => 'id',
                'nullable' => true,
                'onDelete' => 'SET NULL',
            ],
        ];

        $metadata->mapManyToMany($attributeMapping)->shouldBeCalled();
        $metadata->mapManyToMany($optionMapping)->shouldBeCalled();
        $metadata->mapManyToOne($parentMapping)->shouldBeCalled();

        $this->loadClassMetadata($eventArgs);
    }
}
