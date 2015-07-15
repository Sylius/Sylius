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
use Prophecy\Argument;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Extension\DateExtension');
    }

    public function it_should_extends_abstract_type_extension()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractTypeExtension');
    }

    public function it_should_build_the_view_by_default(FormView $view, FormInterface $form)
    {
        $this->buildView($view, $form, array(
            'widget' => 'single_text',
            'format' => 'M/d/y',
            'placeholder' => null,
            'language' => 'fr',
            'leading_zero' => false,
        ));
    }

    public function it_should_configure_the_resolver(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(Argument::type('array'))->shouldBeCalled();

        $resolver->setOptional(array(
            'placeholder',
            'language',
            'leading_zero',
        ))->shouldBeCalled();

        $resolver->setAllowedTypes(array(
            'placeholder' => array('string'),
            'language' => array('string'),
            'leading_zero' => array('bool'),
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_has_extended_type()
    {
        $this->getExtendedType()->shouldReturn('date');
    }
}
