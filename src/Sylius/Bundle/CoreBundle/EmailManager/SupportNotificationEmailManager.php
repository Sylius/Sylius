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
use Sylius\Component\Support\Provider\NotificationRecipientProviderInterface;

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
     * @var NotificationRecipientProviderInterface
     */
    protected $emailProvider;

    /**
     * @param SenderInterface                        $emailSender
     * @param NotificationRecipientProviderInterface $emailProvider
     */
    public function __construct(SenderInterface $emailSender, NotificationRecipientProviderInterface $emailProvider)
    {
        $this->emailSender = $emailSender;
        $this->emailProvider = $emailProvider;
    }

    public function sendNotificationEmail()
    {
        $this->emailSender->send(Emails::SUPPORT_NOTIFICATION, $this->emailProvider->getEmails(), array());
    }
}
