<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\MailerBundle\Sender\Adapter;

use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\SyliusMailerEvents;

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
    public function send(
        array $recipients,
        string $senderAddress,
        string $senderName,
        RenderedEmail $renderedEmail,
        EmailInterface $email,
        array $data,
        array $attachments = [],
        array $replyTo = []
    ): void {
        $message = (new \Swift_Message())
            ->setSubject($renderedEmail->getSubject())
            ->setFrom([$senderAddress => $senderName])
            ->setTo($recipients)
            ->setReplyTo($replyTo);

        $message->setBody($renderedEmail->getBody(), 'text/html');

        foreach ($attachments as $attachment) {
            $file = \Swift_Attachment::fromPath($attachment);

            $message->attach($file);
        }

        $emailSendEvent = new EmailSendEvent($message, $email, $data, $recipients, $replyTo);

        $this->dispatcher->dispatch(SyliusMailerEvents::EMAIL_PRE_SEND, $emailSendEvent);

        $this->mailer->send($message);

        $this->dispatcher->dispatch(SyliusMailerEvents::EMAIL_POST_SEND, $emailSendEvent);
    }
}
