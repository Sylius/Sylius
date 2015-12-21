<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('CountryModel', SyliusResourceBundle::DRIVER_DOCTRINE_ORM, 'sylius_country_choice');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType');
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_country_choice');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_has_a_parent_type_for_orm_driver()
    {
        $this->beConstructedWith('CountryModel', SyliusResourceBundle::DRIVER_DOCTRINE_ORM, 'sylius_country_choice');

        $this->getParent()->shouldReturn('entity');
    }

    function it_has_a_parent_type_for_mongodb_odm_driver()
    {
        $this->beConstructedWith(
            'CountryModel',
            SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM,
            'sylius_country_choice'
        );

        $this->getParent()->shouldReturn('document');
    }

    function it_has_a_parent_type_for_phpcr_odm_driver()
    {
        $this->beConstructedWith(
            'CountryModel',
            SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM,
            'sylius_country_choice'
        );

        $this->getParent()->shouldReturn('phpcr_document');
    }

    function it_defines_resource_options(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'class' => null,
            ))
            ->willReturn($resolver)
        ;
        $resolver
            ->setNormalizers(Argument::any())
            ->willReturn($resolver)
        ;

        $this->configureOptions($resolver);
    }
}
