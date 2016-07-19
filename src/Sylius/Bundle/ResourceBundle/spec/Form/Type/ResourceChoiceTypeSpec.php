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
use Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Exception\UnknownDriverException;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ResourceChoiceTypeSpec extends ObjectBehavior
{
    function let(MetadataInterface $metadata)
    {
        $this->beConstructedWith($metadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType');
    }

    function it_throws_unknown_driver_exception_when_constructing_with_invalid_driver(MetadataInterface $metadata)
    {
        $metadata->getClass('model')->willReturn('CountryModel');
        $metadata->getDriver()->willReturn('badDriver');

        $this->shouldThrow(UnknownDriverException::class)->during('getParent');
    }

    function it_has_a_valid_name(MetadataInterface $metadata)
    {
        $metadata->getName()->willReturn('country');
        $metadata->getApplicationName()->willReturn('sylius');

        $this->getName()->shouldReturn('sylius_country_choice');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_has_a_parent_type_for_orm_driver(MetadataInterface $metadata)
    {
        $metadata->getDriver()->willReturn(SyliusResourceBundle::DRIVER_DOCTRINE_ORM);

        $this->getParent()->shouldReturn('entity');
    }

    function it_has_a_parent_type_for_mongodb_odm_driver(MetadataInterface $metadata)
    {
        $metadata->getDriver()->willReturn(SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM);

        $this->getParent()->shouldReturn('document');
    }

    function it_has_a_parent_type_for_phpcr_odm_driver(MetadataInterface $metadata)
    {
        $metadata->getDriver()->willReturn(SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM);

        $this->getParent()->shouldReturn('phpcr_document');
    }

    function it_defines_resource_options(OptionsResolver $resolver, MetadataInterface $metadata)
    {
        $metadata->getDriver()->willReturn(SyliusResourceBundle::DRIVER_DOCTRINE_ORM);
        $resolver
            ->setDefaults([
                'class' => null,
            ])
            ->shouldBeCalled()
            ->willReturn($resolver)
        ;
        $resolver
            ->setNormalizer('class', Argument::type('callable'))
            ->shouldBeCalled()
            ->willReturn($resolver)
        ;

        $this->configureOptions($resolver);
    }
}
