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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Storage\StorageInterface;
use Sylius\Component\User\Context\CustomerContextInterface;

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
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @var ObjectManager
     */
    private $customerManager;

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
     * @param CustomerContextInterface $customerContext
     * @param ObjectManager $customerManager
     * @param ChannelContextInterface $channelContext
     * @param string $defaultCurrencyCode
     */
    public function __construct(
        StorageInterface $storage,
        CustomerContextInterface $customerContext,
        ObjectManager $customerManager,
        ChannelContextInterface $channelContext,
        $defaultCurrencyCode
    ) {
        $this->storage = $storage;
        $this->customerContext = $customerContext;
        $this->customerManager = $customerManager;
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
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerContext->getCustomer();
        if (null !== $customer && null !== $customer->getCurrencyCode()) {
            return $customer->getCurrencyCode();
        }

        return $this->storage->getData($this->getStorageKey(), $this->defaultCurrencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrencyCode($currencyCode)
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerContext->getCustomer();
        if (null === $customer) {
            $this->storage->setData($this->getStorageKey(), $currencyCode);

            return;
        }

        $customer->setCurrencyCode($currencyCode);

        $this->customerManager->persist($customer);
        $this->customerManager->flush();
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
