<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\ApiBundle\Command\PickupCart;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PickupCartHandler implements MessageHandlerInterface
{
    /** @var FactoryInterface */
    private $cartFactory;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var ObjectManager */
    private $orderManager;

    /** @var RandomnessGeneratorInterface */
    private $generator;

    public function __construct(
        FactoryInterface $cartFactory,
        ChannelContextInterface $channelContext,
        ObjectManager $orderManager,
        RandomnessGeneratorInterface $generator
    ) {
        $this->cartFactory = $cartFactory;
        $this->channelContext = $channelContext;
        $this->orderManager = $orderManager;
        $this->generator = $generator;
    }

    public function __invoke(PickupCart $pickupCart)
    {
        /** @var OrderInterface $cart */
        $cart = $this->cartFactory->createNew();

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        /** @var LocaleInterface $locale */
        $locale = $channel->getDefaultLocale();
        /** @var CurrencyInterface $currency */
        $currency = $channel->getBaseCurrency();

        $cart->setChannel($channel);
        $cart->setLocaleCode($locale->getCode());
        $cart->setCurrencyCode($currency->getCode());
        $cart->setTokenValue($this->generator->generateUriSafeString(10));

        $this->orderManager->persist($cart);

        return $cart;
    }
}
