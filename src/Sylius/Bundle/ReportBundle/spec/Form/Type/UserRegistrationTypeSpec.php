<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReportBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ReportBundle\DataFetcher\UserRegistrationDataFetcher;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserRegistrationTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReportBundle\Form\Type\UserRegistrationType');
    }

    function it_extends_abstract_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_data_fetcher_user_registration');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('start', 'date', Argument::type('array'))
            ->willReturn($builder)
        ;
        $builder
            ->add('end', 'date', Argument::type('array'))
            ->willReturn($builder)
        ;
        $builder
            ->add('period', 'choice', Argument::type('array'))
            ->willReturn($builder)
        ;
        $builder
            ->add('empty_records', 'checkbox', Argument::type('array'))
            ->willReturn($builder)
        ;
        
        $this->buildForm($builder, array());
    }
}