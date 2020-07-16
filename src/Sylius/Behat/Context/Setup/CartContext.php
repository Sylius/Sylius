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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CartContext implements Context
{
    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @Given /^I added (product "[^"]+") to the (cart)$/
     * @Given /^I have (product "[^"]+") in the (cart)$/
     */
    public function iAddedProductToTheCart(ProductInterface $product, OrderInterface $cart): void
    {
        $this->commandBus->dispatch(AddItemToCart::createFromData($cart->getTokenValue(), $product->getCode(), 1));
    }
}
