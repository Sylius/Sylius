<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\RbacBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\ResourceBundle\SyliusResourceBundle;
use Sylius\Resource\Metadata\MetadataInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class RoleEntityTypeSpec extends ObjectBehavior
{
    function let(MetadataInterface $metadata)
    {
        $this->beConstructedWith($metadata);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\RbacBundle\Form\Type\RoleEntityType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType(ResourceChoiceType::class);
    }

    function it_has_options(OptionsResolver $resolver, $metadata)
    {
        $metadata->getDriver()->willReturn(SyliusResourceBundle::DRIVER_DOCTRINE_ORM);
        $resolver->setDefaults(Argument::withKey('class'))->shouldBeCalled()->willReturn($resolver);
        $resolver->setNormalizer('class', Argument::type('callable'))->shouldBeCalled()->willReturn($resolver);
        $resolver->setDefaults(Argument::withKey('query_builder'))->shouldBeCalled()->willReturn($resolver);

        $this->configureOptions($resolver);
    }
}
