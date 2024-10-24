<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    public function __construct(
        private ObjectManager $orderManager,
        private ExpiredCartsRemoverInterface $expiredCartsRemover,
    ) {
    }

    /**
     * @Given /^(?:|he|she) abandoned (the cart) (\d+) (day|days|hour|hours) ago$/
     */
    public function theyAbandonedTheirCart(OrderInterface $cart, $amount, $time)
    {
        $cart->setUpdatedAt(new \DateTime('-' . $amount . ' ' . $time));
        $this->orderManager->flush();
    }

    /**
     * @Then /^(this cart) should be automatically deleted$/
     */
    public function thisCartShouldBeAutomaticallyDeleted(OrderInterface $cart)
    {
        $this->expiredCartsRemover->remove();

        Assert::null($cart->getId());
    }

    /**
     * @Then /^(this cart) should not be deleted$/
     */
    public function thisCartShouldNotBeDeleted(OrderInterface $cart)
    {
        $this->expiredCartsRemover->remove();

        Assert::notNull($cart->getId());
    }
}
