<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Support\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Support\Adapter\ConfigNotificationRecipientAdapterInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class NotificationRecipientProviderSpec extends ObjectBehavior
{
    function let(ConfigNotificationRecipientAdapterInterface $configNotificationRecipientAdapter) {
        $this->beConstructedWith($configNotificationRecipientAdapter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Support\Provider\NotificationRecipientProvider');
    }

    function it_implements_notification_recipient_provider_interface()
    {
        $this->shouldImplement('Sylius\Component\Support\Provider\NotificationRecipientProviderInterface');
    }

    function it_provides_emails($configNotificationRecipientAdapter)
    {
        $configNotificationRecipientAdapter->getEmails()->willReturn(array(
            'example@example.com',
            'contact@example.com',
        ))->shouldBeCalled();

        $this->getEmails()->shouldReturn(array(
            'example@example.com',
            'contact@example.com',
        ));
    }
}
