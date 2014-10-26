<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bridge\Mailer\Adapter;

use Stampie\MailerInterface;
use Sylius\Bridge\Mailer\Adapter\Stampie\Message;
use Sylius\Bridge\Mailer\TwigMailerInterface;

class StampieAdapter extends AbstractAdapter implements TwigMailerInterface
{
    /**
     * @var MailerInterface
     */
    protected $mailer;

    public function __construct(\Twig_Environment $twig, MailerInterface $mailer)
    {
        parent::__construct($twig);

        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function send($templateName, $context, $fromEmail, $toEmail)
    {
        list($subject, $textBody, $htmlBody) = $this->parseTemplate($templateName, $context);

        $message = new Message($toEmail);
        $message->setFrom($fromEmail);
        $message->setSubject($subject);
        $message->setText($textBody);

        if (!empty($htmlBody)) {
            $message->setHtml($htmlBody);
        }

        $this->mailer->send($message);
    }
}
