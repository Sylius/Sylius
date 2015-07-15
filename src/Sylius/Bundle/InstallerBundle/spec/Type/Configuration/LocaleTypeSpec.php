<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InstallerBundle\Form\Type\Configuration;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;

class LocaleTypeSpec extends ObjectBehavior
{
    public function it_is_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    public function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('sylius_locale', 'locale', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('sylius_currency', 'choice', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }
}
