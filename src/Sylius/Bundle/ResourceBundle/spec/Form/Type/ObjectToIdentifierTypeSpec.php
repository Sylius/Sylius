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
use Symfony\Component\Form\Test\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ObjectToIdentifierTypeSpec extends ObjectBehavior
{
    function let(ManagerRegistry $manager)
    {
        $this->beConstructedWith($manager, 'name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\ObjectToIdentifierType');
    }

    function it_build_a_form($manager, FormBuilderInterface $builder, ObjectRepository $repository)
    {
        $manager->getRepository('class')->willReturn($repository);

        $builder->addModelTransformer(
            Argument::type('Sylius\Bundle\ResourceBundle\Form\DataTransformer\ObjectToIdentifierTransformer')
        )->shouldBeCalled();

        $this->buildForm($builder, array(
            'class' => 'class',
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
        $this->getParent()->shouldReturn('entity');
    }

    function it_has_a_parent()
    {
        $this->getName()->shouldReturn('name');
    }
}
