<?php

namespace spec\Sylius\Component\Attribute\Model;

use Sylius\Component\Attribute\Model\AttributeSelectOptionTranslation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Attribute\Model\AttributeSelectOptionTranslationInterface;

class AttributeSelectOptionTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeSelectOptionTranslation::class);
    }

    function it_implements_attribute_translation_interface(): void
    {
        $this->shouldImplement(AttributeSelectOptionTranslationInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Size');
        $this->getName()->shouldReturn('Size');
    }
}
