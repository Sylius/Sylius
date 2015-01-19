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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceHiddenTypeSpec extends ObjectBehavior
{
    function let(ResourceRepositoryInterface $repository)
    {
        $this->beConstructedWith($repository, 'sylius_product_hidden');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\ResourceHiddenType');
    }

    function it_build_a_form(FormBuilderInterface $builder)
    {
        $builder->addViewTransformer(
            Argument::type('Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer')
        )->willReturn($builder);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, Argument::type('\Closure'))->willReturn($builder);
        $builder->addEventListener(FormEvents::SUBMIT, Argument::type('\Closure'))->willReturn($builder);

        $this->buildForm($builder, array(
            'identifier' => 'identifier',
        ));
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'identifier' => 'id',
        ))->shouldBeCalled($resolver);

        $this->configureOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getParent()->shouldReturn('hidden');
    }

    function it_has_a_parent()
    {
        $this->getName()->shouldReturn('sylius_product_hidden');
    }
}
