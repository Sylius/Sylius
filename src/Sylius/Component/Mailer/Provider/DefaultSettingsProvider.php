<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Mailer\Provider;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
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
    public function __construct($senderName, $senderAddress)
    {
        $this->senderName = $senderName;
        $this->senderAddress = $senderAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderAddress()
    {
        return $this->senderAddress;
    }
}
