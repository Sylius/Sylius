<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Myke Hines <myke@webhines.com>
 */
class CommentTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('Comment', ['sylius']);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Form\Type\CommentType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    public function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder->add('comment', 'textarea', Argument::any())->willReturn($builder);
        $builder->add('notifyCustomer', 'checkbox', Argument::any())->willReturn($builder);

        $this->buildForm($builder, []);
    }

    public function it_defines_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'Comment',
                'validation_groups' => ['sylius'],
            ])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }

    public function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_comment');
    }
}
