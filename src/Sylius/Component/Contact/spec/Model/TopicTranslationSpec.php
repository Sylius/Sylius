<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Contact\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Contact\Model\TopicTranslationInterface;

/**
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class TopicTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Contact\Model\TopicTranslation');
    }

    function it_implements_Sylius_topic_translation_interface()
    {
        $this->shouldImplement(TopicTranslationInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_title_by_default()
    {
        $this->getTitle()->shouldReturn(null);
    }

    function its_title_is_mutable()
    {
        $this->setTitle('Title');
        $this->getTitle()->shouldReturn('Title');
    }

    function it_returns_title_when_converted_to_string()
    {
        $this->setTitle('Title');
        $this->__toString()->shouldReturn('Title');
    }
}
