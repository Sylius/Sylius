<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariationBundle\EventListener;

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
                'variable' => 'Some\App\Product\Entity\Product',
                'option' => [
                    'classes' => [
                        'model' => 'Some\App\Product\Entity\Option',
                    ],
                ],
                'option_value' => [
                    'classes' => [
                        'model' => 'Some\App\Product\Entity\OptionValue',
                    ],
                ],
                'variant' => [
                    'classes' => [
                        'model' => 'Some\App\Product\Entity\Variant',
                    ],
                ],
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\EventListener\LoadMetadataSubscriber');
    }

    function it_is_a_Doctrine_event_subscriber()
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    function it_subscribes_to_loadClassMetadata_events_dispatched_by_Doctrine()
    {
        $this->getSubscribedEvents()->shouldReturn(['loadClassMetadata']);
    }

    function it_does_not_add_mapping_if_the_class_is_not_a_configured_for_a_variation_set(LoadClassMetadataEventArgs $eventArgs, ClassMetadataInfo $metadata)
    {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('KeepMoving\ThisClass\DoesNot\Concern\You');

        $metadata->mapManyToMany(Argument::any())->shouldNotBeCalled();
        $metadata->mapOneToMany(Argument::any())->shouldNotBeCalled();
        $metadata->mapManyToOne(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_maps_associations_from_the_variant_model_to_the_variable_object_model_and_the_option_value_model(LoadClassMetadataEventArgs $eventArgs, ClassMetadataInfo $metadata)
    {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('Some\App\Product\Entity\Variant');

        $objectMapping = [
            'fieldName' => 'object',
            'targetEntity' => 'Some\App\Product\Entity\Product',
            'inversedBy' => 'variants',
            'joinColumns' => [[
                'name' => 'product_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];

        $optionsMapping = [
            'fieldName' => 'options',
            'type' => ClassMetadataInfo::MANY_TO_MANY,
            'targetEntity' => 'Some\App\Product\Entity\OptionValue',
            'joinTable' => [
                'name' => 'sylius_product_variant_option_value',
                'joinColumns' => [[
                    'name' => 'variant_id',
                    'referencedColumnName' => 'id',
                    'unique' => false,
                    'nullable' => false,
                    'onDelete' => 'CASCADE',
                ]],
                'inverseJoinColumns' => [[
                    'name' => 'option_value_id',
                    'referencedColumnName' => 'id',
                    'unique' => false,
                    'nullable' => false,
                    'onDelete' => 'CASCADE',
                ]],
            ],
        ];

        $metadata->mapManyToOne($objectMapping)->shouldBeCalled();
        $metadata->mapManyToMany($optionsMapping)->shouldBeCalled();

        $nonApplicableOptionMapping = [
            'fieldName' => 'option',
            'targetEntity' => 'Some\App\Product\Entity\Option',
            'inversedBy' => 'values',
            'joinColumns' => [[
                'name' => 'option_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];

        $metadata->mapOneToMany(Argument::any())->shouldNotBeCalled();
        $metadata->mapManyToOne($nonApplicableOptionMapping)->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_maps_one_to_many_association_from_the_option_model_to_the_option_value_model(LoadClassMetadataEventArgs $eventArgs, ClassMetadataInfo $metadata)
    {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('Some\App\Product\Entity\Option');

        $valuesMapping = [
            'fieldName' => 'values',
            'targetEntity' => 'Some\App\Product\Entity\OptionValue',
            'mappedBy' => 'option',
            'orphanRemoval' => true,
            'cascade' => ['all'],
        ];

        $metadata->mapOneToMany($valuesMapping)->shouldBeCalled();

        $metadata->mapManyToMany(Argument::any())->shouldNotBeCalled();
        $metadata->mapManyToOne(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_maps_many_to_one_association_from_the_option_value_model_to_the_option_model(LoadClassMetadataEventArgs $eventArgs, ClassMetadataInfo $metadata)
    {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('Some\App\Product\Entity\OptionValue');

        $optionMapping = [
            'fieldName' => 'option',
            'targetEntity' => 'Some\App\Product\Entity\Option',
            'inversedBy' => 'values',
            'joinColumns' => [[
                'name' => 'option_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];

        $nonApplicableOptionsMapping = [
            'fieldName' => 'options',
            'type' => ClassMetadataInfo::MANY_TO_MANY,
            'targetEntity' => 'Some\App\Product\Entity\OptionValue',
            'joinTable' => [
                'name' => 'sylius_product_variant_option_value',
                'joinColumns' => [[
                    'name' => 'variant_id',
                    'referencedColumnName' => 'id',
                    'unique' => false,
                    'nullable' => false,
                    'onDelete' => 'CASCADE',
                ]],
                'inverseJoinColumns' => [[
                    'name' => 'option_value_id',
                    'referencedColumnName' => 'id',
                    'unique' => false,
                    'nullable' => false,
                    'onDelete' => 'CASCADE',
                ]],
            ],
        ];

        $metadata->mapManyToOne($optionMapping)->shouldBeCalled();
        $metadata->mapManyToOne($nonApplicableOptionsMapping)->shouldNotBeCalled();
        $metadata->mapOneToMany(Argument::any())->shouldNotBeCalled();
        $metadata->mapManyToMany(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }
}
