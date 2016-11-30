<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Attribute\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\Model\AttributeTranslation;
use Sylius\Component\Attribute\Model\AttributeTranslationInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class AttributeTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeTranslation::class);
    }

    function it_implements_attribute_translation_interface()
    {
        $this->shouldImplement(AttributeTranslationInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Size');
        $this->getName()->shouldReturn('Size');
    }
}
