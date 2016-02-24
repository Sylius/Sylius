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

use SendGrid\Email;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\SyliusMailerEvents;

/**
 * Sendgrid sender.
 *
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class SendGridAdapter extends AbstractAdapter
{
    /**
     * @var \SendGrid
     */
    protected $mailer;

    /**
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->mailer = new \SendGrid($apiKey);
    }

    /**
     * {@inheritdoc}
     */
    public function send(
        array $recipients,
        $senderAddress,
        $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data
    ) {
        $message = new Email();

        $message
            ->setSubject($renderedEmail->getSubject())
            ->setFrom($senderAddress)
            ->setFromName($senderName)
            ->setHtml($renderedEmail->getBody());

        foreach ($recipients as $recipient) {
            $message
                ->addTo($recipient);
        }

        $emailSendEvent = new EmailSendEvent($message, $email, $data, $recipients);

        $this->dispatcher->dispatch(SyliusMailerEvents::EMAIL_PRE_SEND, $emailSendEvent);

        $this->mailer->send($message);

        $this->dispatcher->dispatch(SyliusMailerEvents::EMAIL_POST_SEND, $emailSendEvent);
    }
}
