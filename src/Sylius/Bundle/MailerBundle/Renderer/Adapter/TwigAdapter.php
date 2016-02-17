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

use Sylius\Component\Mailer\Event\EmailRenderEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\SyliusMailerEvents;

/**
 * Default Sylius Twig renderer.
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
    public function render(EmailInterface $email, array $data = [])
    {
        if (null !== $email->getTemplate()) {
            $data = $this->twig->mergeGlobals($data);

            /** @var \Twig_Template $template */
            $template = $this->twig->loadTemplate($email->getTemplate());

            $subject = $template->renderBlock('subject', $data);
            $body = $template->renderBlock('body', $data);
        } else {
            $twig = new \Twig_Environment(new \Twig_Loader_Array([]));

            $subjectTemplate = $twig->createTemplate($email->getSubject());
            $bodyTemplate = $twig->createTemplate($email->getContent());

            $subject = $subjectTemplate->render($data);
            $body = $bodyTemplate->render($data);
        }

        /** @var EmailRenderEvent $event */
        $event = $this->dispatcher->dispatch(
            SyliusMailerEvents::EMAIL_PRE_RENDER,
            new EmailRenderEvent(new RenderedEmail($subject, $body))
        );

        return $event->getRenderedEmail();
    }
}
