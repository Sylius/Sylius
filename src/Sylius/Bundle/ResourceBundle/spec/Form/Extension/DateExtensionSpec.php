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
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Form\Extension\DateExtension');
    }

    function it_should_extends_abstract_type_extension()
    {
        $this->shouldHaveType(AbstractTypeExtension::class);
    }

    function it_should_build_the_view_by_default(FormView $view, FormInterface $form)
    {
        $this->buildView($view, $form, [
            'widget' => 'single_text',
            'format' => 'M/d/y',
            'placeholder' => null,
            'language' => 'fr',
            'leading_zero' => false,
        ]);
    }

    function it_should_configure_the_resolver(OptionsResolver $resolver)
    {
        $resolver->setDefaults(Argument::type('array'))->shouldBeCalled();

        $resolver->setDefined([
            'placeholder',
            'language',
            'leading_zero',
        ])->shouldBeCalled();

        $resolver->setAllowedTypes('placeholder', 'string')->shouldBeCalled();
        $resolver->setAllowedTypes('language', 'string')->shouldBeCalled();
        $resolver->setAllowedTypes('leading_zero', 'bool')->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_extended_type()
    {
        $this->getExtendedType()->shouldReturn('date');
    }
}
