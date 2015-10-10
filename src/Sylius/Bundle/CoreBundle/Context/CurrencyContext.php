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
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\UserBundle\Context\CustomerContext;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Currency\Context\CurrencyContext as BaseCurrencyContext;
use Sylius\Component\Storage\StorageInterface;

/**
 * Core currency context, which is aware of multiple channels.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyContext extends BaseCurrencyContext
{
    const STORAGE_KEY = '_sylius_currency_%s';

    protected $securityContext;
    protected $settingsManager;
    protected $customerManager;
    protected $channelContext;

    /**
     * @param StorageInterface $storage
     * @param CustomerContext $customerContext
     * @param SettingsManagerInterface $settingsManager
     * @param ObjectManager $customerManager
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(
        StorageInterface $storage,
        CustomerContext $customerContext,
        SettingsManagerInterface $settingsManager,
        ObjectManager $customerManager,
        ChannelContextInterface $channelContext
    ) {
        $this->customerContext = $customerContext;
        $this->settingsManager = $settingsManager;
        $this->customerManager = $customerManager;
        $this->channelContext = $channelContext;

        parent::__construct($storage, $this->getDefaultCurrency());
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCurrency()
    {
        return $this->settingsManager->loadSettings('sylius_general')->get('currency');
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        $customer = $this->customerContext->getCustomer();
        if ($customer instanceof CustomerInterface) {
            return $customer->getCurrency();
        }

        $channel = $this->channelContext->getChannel();

        return $this->storage->getData($this->getStorageKey($channel->getCode()), $this->defaultCurrency);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $customer = $this->customerContext->getCustomer();
        if (!$customer instanceof CustomerInterface) {
            $channel = $this->channelContext->getChannel();

            $this->storage->setData($this->getStorageKey($channel->getCode()), $currency);

            return;
        }

        $customer->setCurrency($currency);

        $this->customerManager->persist($customer);
        $this->customerManager->flush();
    }

    /**
     * Get storage key for channel with given code.
     *
     * @param string $channelCode
     * @return string
     */
    private function getStorageKey($channelCode)
    {
        return sprintf(self::STORAGE_KEY, $channelCode);
    }
}
