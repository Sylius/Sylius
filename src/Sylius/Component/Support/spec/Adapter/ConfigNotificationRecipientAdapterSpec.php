<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Support\Adapter;

use PhpSpec\ObjectBehavior;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ConfigNotificationRecipientAdapterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array(
            'example@example.com'
        ));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Support\Adapter\ConfigNotificationRecipientAdapter');
    }

    function it_implements_config_notification_recipient_adapter_interface()
    {
        $this->shouldImplement('Sylius\Component\Support\Adapter\ConfigNotificationRecipientAdapterInterface');
    }

    function it_provides_emails()
    {
        $emails = array(
            'example@example.com'
        );

        $this->getEmails()->shouldReturn($emails);
    }
}
