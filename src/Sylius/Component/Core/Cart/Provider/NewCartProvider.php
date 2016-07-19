<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Cart\Provider;

use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class NewCartProvider implements CartProviderInterface
{
    /**
     * @var CartProviderInterface
     */
    private $decoratedCartProvider;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @var CartInterface
     */
    private $cart;

    /**
     * @param CartProviderInterface $decoratedCartProvider
     * @param ShopperContextInterface $shopperContext
     */
    public function __construct(CartProviderInterface $decoratedCartProvider, ShopperContextInterface $shopperContext)
    {
        $this->decoratedCartProvider = $decoratedCartProvider;
        $this->shopperContext = $shopperContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        if (null !== $this->cart) {
            return $this->cart;
        }

        /** @var OrderInterface $cart */
        $cart = $this->decoratedCartProvider->getCart();

        if (null === $cart) {
            throw new \LogicException('Decorated cart provider must return a cart instance, null given.');
        }

        $cart->setChannel($this->shopperContext->getChannel());
        $cart->setCustomer($this->shopperContext->getCustomer());
        $cart->setCurrencyCode($this->shopperContext->getCurrencyCode());
        // Soon... $cart->setLocaleCode($this->shopperContext->getLocaleCode());

        $this->cart = $cart;

        return $cart;
    }
}
