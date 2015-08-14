<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\LocaleBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;

/**
 * @mixin \Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class LocaleChoiceTypeSpec extends ObjectBehavior
{
    function let(ObjectRepository $localeRepository)
    {
        $this->beConstructedWith($localeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_locale_choice');
    }

    function it_has_a_parent_type()
    {
        $this->getParent()->shouldReturn('choice');
    }
}
