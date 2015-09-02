<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Mailer\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Manuel Gonzalez <mgonyan@gmail.com>
 */
class EmailTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Mailer\Model\EmailTranslation');
    }

    function it_extends_abstract_translation()
    {
        $this->shouldHaveType('Sylius\Component\Translation\Model\AbstractTranslation');
    }

    function it_implements_email_translation_interface()
    {
        $this->shouldImplement('Sylius\Component\Mailer\Model\EmailTranslationInterface');
    }

    function it_gets_its_id()
    {
        $this->getId()->shouldReturn(null);
    }

    function its_subject_is_mutable()
    {
        $this->setSubject('subject');
        $this->getSubject()->shouldReturn('subject');
    }

    function its_content_is_mutable()
    {
        $this->setContent('content');
        $this->getContent()->shouldReturn('content');
    }
}
