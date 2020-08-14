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
    /** @var MessageBusInterface */
    private $commandBus;

    /** @var RandomnessGeneratorInterface */
    private $generator;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        MessageBusInterface $commandBus,
        RandomnessGeneratorInterface $generator,
        SharedStorageInterface $sharedStorage
    ) {
        $this->commandBus = $commandBus;
        $this->generator = $generator;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform /^(cart)$/
     */
    public function provideCartToken(): string
    {
        if ($this->sharedStorage->has('cart_token')) {
            return $this->sharedStorage->get('cart_token');
        }

        $tokenValue = $this->generator->generateUriSafeString(10);

        $this->commandBus->dispatch(new PickupCart($tokenValue));

        $this->sharedStorage->set('cart_token', $tokenValue);

        return $tokenValue;
    }
}
