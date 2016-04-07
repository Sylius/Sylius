<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Factory;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AttributeBundle\Factory\AttributeValueTypeConfigurationFactory;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType\Configuration\AttributeValueTypeTranslationConfiguration;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType\Configuration\AttributeValueTypeConfiguration;

/**
 * @mixin AttributeValueTypeConfigurationFactory
 *
 * @author Salvatore Pappalardo <salvatore.pappalardo82@gmail.com>
 */
class AttributeValueTypeConfigurationFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeValueTypeConfigurationFactory::class);
    }

    function it_creates_base_attribute_value_type_configuration(AttributeInterface $attribute)
    {
        $attribute->isTranslatable()->willReturn(false);

        $this->create($attribute, 'server', 0)->shouldReturnAnInstanceOf(AttributeValueTypeConfiguration::class);
    }

    function it_creates_translation_attribute_value_type_configuration(AttributeInterface $attribute)
    {
        $attribute->isTranslatable()->willReturn(true);

        $this->create($attribute, 'server', 0)->shouldReturnAnInstanceOf(AttributeValueTypeTranslationConfiguration::class);
    }
}
