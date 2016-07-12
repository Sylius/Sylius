<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Storage\StorageInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CurrencyContext implements CurrencyContextInterface
{
    const STORAGE_KEY = '_sylius_currency_%s';

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var string
     */
    private $defaultCurrencyCode;

    /**
     * @param StorageInterface $storage
     * @param ChannelContextInterface $channelContext
     * @param string $defaultCurrencyCode
     */
    public function __construct(StorageInterface $storage, ChannelContextInterface $channelContext, $defaultCurrencyCode)
    {
        $this->storage = $storage;
        $this->channelContext = $channelContext;
        $this->defaultCurrencyCode = $defaultCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCurrencyCode()
    {
        return $this->defaultCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode()
    {
        return $this->storage->getData($this->getStorageKey(), $this->defaultCurrencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->storage->setData($this->getStorageKey(), $currencyCode);
    }

    /**
     * @return string
     */
    private function getStorageKey()
    {
        try {
            return sprintf(self::STORAGE_KEY, $this->channelContext->getChannel()->getCode());
        } catch (ChannelNotFoundException $exception) {
            return sprintf(self::STORAGE_KEY, '__DEFAULT__');
        }
    }
}
