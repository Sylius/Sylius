<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Variation\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Variation\Model\OptionTranslationInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class OptionTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Variation\Model\OptionTranslation');
    }

    function it_implement_Sylius_option_interface()
    {
        $this->shouldImplement(OptionTranslationInterface::class);
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable()
    {
        $this->setName('Size');
        $this->getName()->shouldReturn('Size');
    }
}
