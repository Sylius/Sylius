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

class EmailSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Mailer\Model\Email');
    }

    public function it_implements_Sylius_email_interface()
    {
        $this->shouldImplement('Sylius\Component\Mailer\Model\EmailInterface');
    }

    public function it_does_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    public function its_code_is_mutable()
    {
        $this->setCode('bar');
        $this->getCode()->shouldReturn('bar');
    }

    public function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    public function it_can_be_disabled()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    public function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    public function it_does_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
