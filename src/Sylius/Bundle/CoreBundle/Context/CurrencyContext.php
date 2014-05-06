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
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Currency\Context\CurrencyContext as BaseCurrencyContext;
use Sylius\Component\Storage\StorageInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Core currency context, which is aware of multiple channels.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyContext extends BaseCurrencyContext
{
    const STORAGE_KEY = '_sylius.currency.%s';

    protected $securityContext;
    protected $settingsManager;
    protected $userManager;
    protected $channelContext;

    /**
     * @param StorageInterface         $storage
     * @param SecurityContextInterface $securityContext
     * @param SettingsManagerInterface $settingsManager
     * @param ObjectManager            $userManager
     * @param ChannelContextInterface  $channelContext
     */
    public function __construct(
        StorageInterface $storage,
        SecurityContextInterface $securityContext,
        SettingsManagerInterface $settingsManager,
        ObjectManager $userManager,
        ChannelContextInterface $channelContext
    ) {
        $this->securityContext = $securityContext;
        $this->settingsManager = $settingsManager;
        $this->userManager = $userManager;
        $this->channelContext = $channelContext;

        parent::__construct($storage, $this->getDefaultCurrency());
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCurrency()
    {
        return $this->settingsManager->loadSettings('general')->get('currency');
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        if ((null !== $user = $this->getUser()) && null !== $user->getCurrency()) {
            return $user->getCurrency();
        }

        $channel = $this->channelContext->getChannel();

        return $this->storage->getData($this->getStorageKey($channel->getCode()), $this->defaultCurrency);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $channel = $this->channelContext->getChannel();
        $this->storage->setData($this->getStorageKey($channel->getCode()), $currency);

        if (null === $user = $this->getUser()) {
            return;
        }

        $user->setCurrency($currency);

        $this->userManager->persist($user);
        $this->userManager->flush();
    }

    /**
     * Get currently logged in user.
     *
     * @return null|UserInterface
     */
    protected function getUser()
    {
        if ($this->securityContext->getToken() && $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->securityContext->getToken()->getUser();
        }
    }

    /**
     * Get storage key for channel with given code.
     *
     * @param string $channelCode
     */
    private function getStorageKey($channelCode)
    {
        return sprintf(self::STORAGE_KEY, $channelCode);
    }
}
