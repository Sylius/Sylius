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
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;
use Symfony\Component\Form\Test\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceToIdentifierTypeSpec extends ObjectBehavior
{
    function let(ResourceRepositoryInterface $repository)
    {
        $this->beConstructedWith($repository, 'name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\ResourceToIdentifierType');
    }

    function it_build_a_form(FormBuilderInterface $builder)
    {
        $builder->addModelTransformer(
            Argument::type('Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer')
        )->shouldBeCalled();

        $this->buildForm($builder, array(
            'identifier' => 'identifier',
        ));
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'identifier' => 'id'
        ))->willReturn($resolver);

        $resolver->setAllowedTypes('identifier', 'string')->willReturn($resolver);

        $this->configureOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getParent()->shouldReturn('text');
    }

    function it_has_a_parent()
    {
        $this->getName()->shouldReturn('name');
    }
}
