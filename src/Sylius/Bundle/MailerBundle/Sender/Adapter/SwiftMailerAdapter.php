<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MailerBundle\Sender\Adapter;

use Sylius\Component\Mailer\Event\EmailEvent;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;

/**
 * Default Sylius mailer using Twig and Swiftmailer.
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class SwiftMailerAdapter extends AbstractAdapter
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function send(array $recipients, $senderAddress, $senderName, RenderedEmail $renderedEmail)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($renderedEmail->getSubject())
            ->setFrom(array($senderAddress => $senderName))
            ->setTo($recipients);

        $message->setBody($renderedEmail->getBody(), 'text/html');

        $this->mailer->send($message);

        $this->dispatcher->dispatch(self::EVENT_EMAIL_SENT, new EmailEvent($renderedEmail, $recipients));
    }
}
