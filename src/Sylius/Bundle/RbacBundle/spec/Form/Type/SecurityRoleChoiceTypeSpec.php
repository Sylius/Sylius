<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\RbacBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class SecurityRoleChoiceTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(array(
            'key' => 'role',
        ));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\RbacBundle\Form\Type\SecurityRoleChoiceType');
    }

    public function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    public function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                'key' => 'role',
            ),
            'multiple' => true,
            'expanded' => true,
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_security_role_choice');
    }
}
