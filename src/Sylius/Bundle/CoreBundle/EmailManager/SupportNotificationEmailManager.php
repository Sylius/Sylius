<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EmailManager;

use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Support\Provider\ArrayRecipientsProviderInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class SupportNotificationEmailManager
{
    /**
     * @var SenderInterface
     */
    protected $emailSender;

    /**
     * @var ArrayRecipientsProviderInterface
     */
    protected $emailProvider;

    /**
     * @param SenderInterface                        $emailSender
     * @param ArrayRecipientsProviderInterface $emailProvider
     */
    public function __construct(SenderInterface $emailSender, ArrayRecipientsProviderInterface $emailProvider)
    {
        $this->emailSender = $emailSender;
        $this->emailProvider = $emailProvider;
    }

    public function sendNotificationEmail()
    {
        $this->emailSender->send(Emails::SUPPORT_NOTIFICATION, $this->emailProvider->getEmails(), array());
    }
}
