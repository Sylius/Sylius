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

use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface;

/**
 * Default Sylius mailer using Twig and SwiftMailer.
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TwigSwiftMailerAdapter extends AbstractAdapter implements AdapterInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param DefaultSettingsProviderInterface $defaultSettingsProvider
     * @param \Swift_Mailer                    $mailer
     * @param \Twig_Environment                $twig
     */
    public function __construct(
        DefaultSettingsProviderInterface $defaultSettingsProvider,
        \Swift_Mailer $mailer,
        \Twig_Environment $twig
    ) {
        parent::__construct($defaultSettingsProvider);

        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function send(EmailInterface $email, array $recipients, array $data = array())
    {
        if (null !== $email->getTemplate()) {
            $data = $this->twig->mergeGlobals($data);

            $template = $this->twig->loadTemplate($email->getTemplate());

            $subject = $template->renderBlock('subject', $data);
            $body = $template->renderBlock('body', $data);
        } else {
            $twig = new \Twig_Environment(new \Twig_Loader_Array(array()));

            $subjectTemplate = $twig->createTemplate($email->getSubject());
            $bodyTemplate = $twig->createTemplate($email->getContent());

            $subject = $subjectTemplate->render($data);
            $body = $bodyTemplate->render($data);
        }

        $senderAddress = $email->getSenderAddress() ?: $this->defaultSettingsProvider->getSenderAddress();
        $senderName = $email->getSenderName() ?: $this->defaultSettingsProvider->getSenderName();

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom(array($senderAddress => $senderName))
            ->setTo($recipients);

        $message->setBody($body, 'text/html');

        $this->mailer->send($message);
    }
}
