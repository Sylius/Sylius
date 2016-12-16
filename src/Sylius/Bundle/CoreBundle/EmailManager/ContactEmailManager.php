<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EmailManager;

use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ContactEmailManager
{
    /**
     * @var SenderInterface
     */
    private $emailSender;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param SenderInterface $emailSender
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(SenderInterface $emailSender, ChannelContextInterface $channelContext)
    {
        $this->emailSender = $emailSender;
        $this->channelContext = $channelContext;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function sendContactRequest(array $data)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $contactEmail = $channel->getContactEmail();
        if (null === $contactEmail) {
            return false;
        }

        $this->emailSender->send(
            Emails::CONTACT_REQUEST,
            [
                $contactEmail,
            ],
            [
                'data' => $data,
            ]
        );

        return true;
    }
}
