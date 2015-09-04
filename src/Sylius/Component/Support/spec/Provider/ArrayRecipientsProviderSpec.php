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

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ArrayRecipientsProviderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array(
            'example@example.com'
        ));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Support\Provider\ArrayRecipientsProvider');
    }

    function it_implements_notification_recipient_provider_interface()
    {
        $this->shouldImplement('Sylius\Component\Support\Provider\ArrayRecipientsProviderInterface');
    }

    function it_provides_emails()
    {
        $emails = array(
            'example@example.com'
        );

        $this->getEmails()->shouldReturn($emails);
    }
}
