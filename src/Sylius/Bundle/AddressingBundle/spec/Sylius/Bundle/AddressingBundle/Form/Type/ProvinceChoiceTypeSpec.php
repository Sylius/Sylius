<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class ProvinceChoiceTypeSpec extends ObjectBehavior
{
    function let(EntityRepository $entityRepository)
    {
        $this->beConstructedWith($entityRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\Type\ProvinceChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_province_choice');
    }

    function it_has_a_parent_type()
    {
        $this->getParent()->shouldReturn('choice');
    }
}
