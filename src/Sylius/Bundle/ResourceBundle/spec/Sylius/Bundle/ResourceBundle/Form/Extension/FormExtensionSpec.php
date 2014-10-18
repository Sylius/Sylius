<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Form\Extension;

use PhpSpec\ObjectBehavior;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FormExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Extension\FormExtension');
    }

    public function it_is_a_type_extension()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractTypeExtension');
    }

    public function it_configures_the_resolver(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_extends_form_type()
    {
        $this->getExtendedType()->shouldReturn('form');
    }
}
