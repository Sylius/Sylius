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

namespace Sylius\Component\Mailer\Event;

use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Symfony\Component\EventDispatcher\Event;

class EmailRenderEvent extends Event
{
    /**
     * @var RenderedEmail
     */
    protected $renderedEmail;

    /**
     * @var string[]
     */
    protected $recipients;

    /**
     * @param RenderedEmail $renderedEmail
     * @param array $recipients
     */
    public function __construct(RenderedEmail $renderedEmail, array $recipients = [])
    {
        $this->renderedEmail = $renderedEmail;
        $this->recipients = $recipients;
    }

    /**
     * @return RenderedEmail
     */
    public function getRenderedEmail(): RenderedEmail
    {
        return $this->renderedEmail;
    }

    /**
     * @param RenderedEmail $renderedEmail
     */
    public function setRenderedEmail(RenderedEmail $renderedEmail): void
    {
        $this->renderedEmail = $renderedEmail;
    }

    /**
     * @return array
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @param array $recipients
     */
    public function setRecipients(array $recipients): void
    {
        $this->recipients = $recipients;
    }
}
