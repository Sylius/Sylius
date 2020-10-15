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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CartTokenContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(SharedStorageInterface $sharedStorage)
    {
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform /^(cart)$/
     */
    public function provideCartToken(): ?string
    {
        if ($this->sharedStorage->has('cart_token')) {
            return $this->sharedStorage->get('cart_token');
        }

        return null;
    }

    /**
     * @Transform /^(customer cart)$/
     * @Transform /^(customer's cart)$/
     * @Transform /^(visitor's cart)$/
     * @Transform /^(their cart)$/
     */
    public function providePreviousCartToken(): ?string
    {
        $tokenValue = $this->provideCartToken();
        if ($tokenValue !== null) {
            return $tokenValue;
        }

        if ($this->sharedStorage->has('previous_cart_token')) {
            return $this->sharedStorage->get('previous_cart_token');
        }

        return null;
    }
}
