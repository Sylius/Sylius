<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MailerBundle\Renderer\Adapter;

use Sylius\Component\Mailer\Event\EmailEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Renderer\RenderedEmail;

/**
 * Default Sylius mailer using Twig and SwiftMailer.
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Jérémy Leherpeur <jeremy@leherpeur.net>
 */
class TwigAdapter extends AbstractAdapter
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function render(EmailInterface $email, array $data = array())
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

        /** @var EmailEvent $event */
        $event = $this->dispatcher->dispatch(self::EVENT_EMAIL_RENDERED, new EmailEvent(new RenderedEmail($subject, $body)));

        return $event->getRenderedEmail();
    }
}
