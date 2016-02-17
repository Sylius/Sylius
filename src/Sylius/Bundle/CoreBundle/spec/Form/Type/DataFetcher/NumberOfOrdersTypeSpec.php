<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type\DataFetcher;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class NumberOfOrdersTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\DataFetcher\NumberOfOrdersType');
    }

    function it_extends_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_data_fetcher_number_of_orders');
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

        $this->buildForm($builder, []);
    }
}
