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

use Sylius\Bridge\Mailer\TwigMailerInterface;

class SwiftmailerAdapter extends AbstractAdapter implements TwigMailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer)
    {
        parent::__construct($twig);

        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     *
     * @author Christophe Coevoet <stof@notk.org>
     */
    public function send($templateName, $context, $fromEmail, $toEmail)
    {
        list($subject, $textBody, $htmlBody) = $this->parseTemplate($templateName, $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
        ;

        if (!empty($htmlBody)) {
            $message
                ->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain')
            ;
        } else {
            $message->setBody($textBody);
        }

        $this->mailer->send($message);
    }
}
