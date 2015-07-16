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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ResourceChoiceTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('CountryModel', SyliusResourceBundle::DRIVER_DOCTRINE_ORM, 'sylius_country_choice');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType');
    }

    public function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_country_choice');
    }

    public function it_should_be_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    public function it_has_a_parent_type_for_orm_driver()
    {
        $this->beConstructedWith('CountryModel', SyliusResourceBundle::DRIVER_DOCTRINE_ORM, 'sylius_country_choice');

        $this->getParent()->shouldReturn('entity');
    }

    public function it_has_a_parent_type_for_mongodb_odm_driver()
    {
        $this->beConstructedWith(
            'CountryModel',
            SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM,
            'sylius_country_choice'
        );

        $this->getParent()->shouldReturn('document');
    }

    public function it_has_a_parent_type_for_phpcr_odm_driver()
    {
        $this->beConstructedWith(
            'CountryModel',
            SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM,
            'sylius_country_choice'
        );

        $this->getParent()->shouldReturn('phpcr_document');
    }

    public function it_defines_resource_options(OptionsResolverInterface $resolver)
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

        $this->setDefaultOptions($resolver);
    }
}
