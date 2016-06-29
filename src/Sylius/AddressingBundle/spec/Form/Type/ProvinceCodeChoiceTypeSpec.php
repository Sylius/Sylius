<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\AddressingBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\AddressingBundle\Form\Type\ProvinceChoiceType;
use Sylius\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ProvinceCodeChoiceTypeSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\AddressingBundle\Form\Type\ProvinceCodeChoiceType');
    }

    function it_extends_province_choice_type()
    {
        $this->shouldHaveType(ProvinceChoiceType::class);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_province_code_choice');
    }

    function it_configures_options(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('choice_list', null)->shouldBeCalled();
        $resolver->setDefault('choices', Argument::type('callable'))->shouldBeCalled();
    }
}
