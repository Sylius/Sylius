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

namespace Sylius\Component\Mailer\Sender;

use Sylius\Component\Mailer\Provider\DefaultSettingsProviderInterface;
use Sylius\Component\Mailer\Provider\EmailProviderInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AdapterInterface as RendererAdapterInterface;
use Sylius\Component\Mailer\Sender\Adapter\AdapterInterface as SenderAdapterInterface;

final class Sender implements SenderInterface
{
    /**
     * @var RendererAdapterInterface
     */
    private $rendererAdapter;

    /**
     * @var SenderAdapterInterface
     */
    private $senderAdapter;

    /**
     * @var EmailProviderInterface
     */
    private $provider;

    /**
     * @var DefaultSettingsProviderInterface
     */
    private $defaultSettingsProvider;

    /**
     * @param RendererAdapterInterface $rendererAdapter
     * @param SenderAdapterInterface $senderAdapter
     * @param EmailProviderInterface $provider
     * @param DefaultSettingsProviderInterface $defaultSettingsProvider
     */
    public function __construct(
        RendererAdapterInterface $rendererAdapter,
        SenderAdapterInterface $senderAdapter,
        EmailProviderInterface $provider,
        DefaultSettingsProviderInterface $defaultSettingsProvider
    ) {
        $this->senderAdapter = $senderAdapter;
        $this->rendererAdapter = $rendererAdapter;
        $this->provider = $provider;
        $this->defaultSettingsProvider = $defaultSettingsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $code, array $recipients, array $data = [], array $attachments = [], array $replyTo = []): void
    {
        $email = $this->provider->getEmail($code);

        if (!$email->isEnabled()) {
            return;
        }

        $senderAddress = $email->getSenderAddress() ?: $this->defaultSettingsProvider->getSenderAddress();
        $senderName = $email->getSenderName() ?: $this->defaultSettingsProvider->getSenderName();

        $renderedEmail = $this->rendererAdapter->render($email, $data);

        $this->senderAdapter->send(
            $recipients,
            $senderAddress,
            $senderName,
            $renderedEmail,
            $email,
            $data,
            $attachments,
            $replyTo
        );
    }
}
