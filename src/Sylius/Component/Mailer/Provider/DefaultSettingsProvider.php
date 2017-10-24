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

namespace Sylius\Component\Mailer\Provider;

final class DefaultSettingsProvider implements DefaultSettingsProviderInterface
{
    /**
     * @var string
     */
    private $senderName;

    /**
     * @var string
     */
    private $senderAddress;

    /**
     * @param string $senderName
     * @param string $senderAddress
     */
    public function __construct(string $senderName, string $senderAddress)
    {
        $this->senderName = $senderName;
        $this->senderAddress = $senderAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderAddress(): string
    {
        return $this->senderAddress;
    }
}
