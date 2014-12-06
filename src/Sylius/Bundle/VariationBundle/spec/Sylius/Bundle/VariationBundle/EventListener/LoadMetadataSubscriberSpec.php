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
        $this->beConstructedWith(array(
            'product' => array(
                'variable' => 'Some\App\Product\Entity\Product',
                'option' => array(
                    'model' => 'Some\App\Product\Entity\Option',
                ),
                'option_value' => array(
                    'model' => 'Some\App\Product\Entity\OptionValue',
                ),
                'variant' => array(
                    'model' => 'Some\App\Product\Entity\Variant',
                ),
            ),
        ));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\EventListener\LoadMetadataSubscriber');
    }

    function it_is_a_Doctrine_event_subscriber()
    {
        $this->shouldImplement('Doctrine\Common\EventSubscriber');
    }

    function it_subscribes_to_loadClassMetadata_events_dispatched_by_Doctrine()
    {
        $this->getSubscribedEvents()->shouldReturn(array('loadClassMetadata'));
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

        $objectMapping = array(
            'fieldName' => 'object',
            'targetEntity' => 'Some\App\Product\Entity\Product',
            'inversedBy' => 'variants',
            'joinColumns' => array(array(
                'name' => 'product_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE'
            ))
        );

        $optionsMapping = array(
            'fieldName' => 'options',
            'type' => ClassMetadataInfo::MANY_TO_MANY,
            'targetEntity' => 'Some\App\Product\Entity\OptionValue',
            'joinTable' => array(
                'name' => 'sylius_product_variant_option_value',
                'joinColumns' => array(array(
                    'name' => 'variant_id',
                    'referencedColumnName' => 'id',
                    'unique' => false,
                    'nullable' => false,
                    'onDelete' => 'CASCADE'
                )),
                'inverseJoinColumns' => array(array(
                    'name' => 'option_value_id',
                    'referencedColumnName' => 'id',
                    'unique' => false,
                    'nullable' => false,
                    'onDelete' => 'CASCADE'
                ))
            )
        );

        $metadata->mapManyToOne($objectMapping)->shouldBeCalled();
        $metadata->mapManyToMany($optionsMapping)->shouldBeCalled();

        $nonApplicableOptionMapping = array(
            'fieldName' => 'option',
            'targetEntity' => 'Some\App\Product\Entity\Option',
            'inversedBy' => 'values',
            'joinColumns' => array(array(
                'name' => 'option_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE'
            ))
        );

        $metadata->mapOneToMany(Argument::any())->shouldNotBeCalled();
        $metadata->mapManyToOne($nonApplicableOptionMapping)->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_maps_one_to_many_association_from_the_option_model_to_the_option_value_model(LoadClassMetadataEventArgs $eventArgs, ClassMetadataInfo $metadata)
    {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('Some\App\Product\Entity\Option');

        $valuesMapping = array(
            'fieldName' => 'values',
            'targetEntity' => 'Some\App\Product\Entity\OptionValue',
            'mappedBy' => 'option',
            'cascade' => array('all')
        );

        $metadata->mapOneToMany($valuesMapping)->shouldBeCalled();

        $metadata->mapManyToMany(Argument::any())->shouldNotBeCalled();
        $metadata->mapManyToOne(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_maps_many_to_one_association_from_the_option_value_model_to_the_option_model(LoadClassMetadataEventArgs $eventArgs, ClassMetadataInfo $metadata)
    {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('Some\App\Product\Entity\OptionValue');

        $optionMapping = array(
            'fieldName' => 'option',
            'targetEntity' => 'Some\App\Product\Entity\Option',
            'inversedBy' => 'values',
            'joinColumns' => array(array(
                'name' => 'option_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE'
            ))
        );

        $nonApplicableOptionsMapping = array(
            'fieldName' => 'options',
            'type' => ClassMetadataInfo::MANY_TO_MANY,
            'targetEntity' => 'Some\App\Product\Entity\OptionValue',
            'joinTable' => array(
                'name' => 'sylius_product_variant_option_value',
                'joinColumns' => array(array(
                    'name' => 'variant_id',
                    'referencedColumnName' => 'id',
                    'unique' => false,
                    'nullable' => false,
                    'onDelete' => 'CASCADE'
                )),
                'inverseJoinColumns' => array(array(
                    'name' => 'option_value_id',
                    'referencedColumnName' => 'id',
                    'unique' => false,
                    'nullable' => false,
                    'onDelete' => 'CASCADE'
                ))
            )
        );

        $metadata->mapManyToOne($optionMapping)->shouldBeCalled();
        $metadata->mapManyToOne($nonApplicableOptionsMapping)->shouldNotBeCalled();
        $metadata->mapOneToMany(Argument::any())->shouldNotBeCalled();
        $metadata->mapManyToMany(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }
}
