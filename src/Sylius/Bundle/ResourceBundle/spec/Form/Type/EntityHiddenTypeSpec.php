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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ObjectToIdentifierTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityHiddenTypeSpec extends ObjectBehavior
{
    function let(ManagerRegistry $manager)
    {
        $this->beConstructedWith($manager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\EntityHiddenType');
    }

    function it_build_a_form($manager, FormBuilderInterface $builder, ObjectRepository $repository)
    {
        $manager->getRepository('data_class')->willReturn($repository);

        $builder->addViewTransformer(
            Argument::type(ObjectToIdentifierTransformer::class)
        )->willReturn($builder);

        $builder->setAttribute('data_class', 'data_class')->willReturn($builder);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, Argument::type(\Closure::class))->willReturn($builder);
        $builder->addEventListener(FormEvents::SUBMIT, Argument::type(\Closure::class))->willReturn($builder);

        $this->buildForm($builder, array(
            'data_class' => 'data_class',
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
        $this->getName()->shouldReturn('entity_hidden');
    }
}
