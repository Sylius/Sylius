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

namespace Sylius\Bundle\MailerBundle\Renderer\Adapter;

use Sylius\Component\Mailer\Event\EmailRenderEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\SyliusMailerEvents;

class EmailTwigAdapter extends AbstractAdapter
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
    public function render(EmailInterface $email, array $data = []): RenderedEmail
    {
        $renderedEmail = $this->getRenderedEmail($email, $data);

        /** @var EmailRenderEvent $event */
        $event = $this->dispatcher->dispatch(
            SyliusMailerEvents::EMAIL_PRE_RENDER,
            new EmailRenderEvent($renderedEmail)
        );

        return $event->getRenderedEmail();
    }

    /**
     * @param EmailInterface $email
     * @param array $data
     *
     * @return RenderedEmail
     */
    private function getRenderedEmail(EmailInterface $email, array $data): RenderedEmail
    {
        if (null !== $email->getTemplate()) {
            return $this->provideEmailWithTemplate($email, $data);
        }

        return $this->provideEmailWithoutTemplate($email, $data);
    }

    /**
     * @param EmailInterface $email
     * @param array $data
     *
     * @return RenderedEmail
     */
    private function provideEmailWithTemplate(EmailInterface $email, array $data): RenderedEmail
    {
        $data = $this->twig->mergeGlobals($data);

        /** @var \Twig_Template $template */
        $template = $this->twig->loadTemplate($email->getTemplate());

        $subject = $template->renderBlock('subject', $data);
        $body = $template->renderBlock('body', $data);

        return new RenderedEmail($subject, $body);
    }

    /**
     * @param EmailInterface $email
     * @param array $data
     *
     * @return RenderedEmail
     */
    private function provideEmailWithoutTemplate(EmailInterface $email, array $data): RenderedEmail
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array([]));

        $subjectTemplate = $twig->createTemplate($email->getSubject());
        $bodyTemplate = $twig->createTemplate($email->getContent());

        $subject = $subjectTemplate->render($data);
        $body = $bodyTemplate->render($data);

        return new RenderedEmail($subject, $body);
    }
}
