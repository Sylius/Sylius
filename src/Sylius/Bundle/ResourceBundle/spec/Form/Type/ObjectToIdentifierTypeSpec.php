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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ObjectToIdentifierTypeSpec extends ObjectBehavior
{
    public function let(ManagerRegistry $manager)
    {
        $this->beConstructedWith($manager, 'name');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Type\ObjectToIdentifierType');
    }

    public function it_build_a_form($manager, FormBuilderInterface $builder, ObjectRepository $repository)
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

    public function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'identifier' => 'id',
        ))->willReturn($resolver);

        $resolver->setAllowedTypes(array(
            'identifier' => array('string'),
        ))->willReturn($resolver);

        $this->setDefaultOptions($resolver);
    }

    public function it_has_a_name()
    {
        $this->getParent()->shouldReturn('entity');
    }

    public function it_has_a_parent()
    {
        $this->getName()->shouldReturn('name');
    }
}
