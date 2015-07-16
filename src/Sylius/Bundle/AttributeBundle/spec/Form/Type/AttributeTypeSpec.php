<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Attribute\Model\AttributeTypes;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Leszek Prabucki <leszek.prabucki@gmail.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class AttributeTypeSpec extends ObjectBehavior
{
    public function let(FormBuilder $builder, FormFactoryInterface $formFactory)
    {
        $this->beConstructedWith('Attribute', array('sylius'), 'server');

        $builder->getFormFactory()->willReturn($formFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Form\Type\AttributeType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    public function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->addEventSubscriber(Argument::type('Sylius\Bundle\AttributeBundle\Form\EventListener\BuildAttributeFormChoicesListener'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('translations', 'a2lix_translationsForms', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('name', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('type', 'choice', array(
                'choices' => AttributeTypes::getChoices(),
                'label' => 'sylius.form.attribute.type',
            ))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    public function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Attribute', 'validation_groups' => array('sylius')))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_server_attribute');
    }
}
