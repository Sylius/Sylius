<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CartContext implements Context
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ObjectManager
     */
    private $orderManager;

    /**
     * @var ExpiredCartsRemoverInterface
     */
    private $expiredCartsRemover;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param ObjectManager $orderManager
     * @param ExpiredCartsRemoverInterface $expiredCartsRemover
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $orderManager,
        ExpiredCartsRemoverInterface $expiredCartsRemover
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderManager = $orderManager;
        $this->expiredCartsRemover = $expiredCartsRemover;
    }

    /**
     * @Given /^(?:|he|she) abandoned (the cart) (\d+) (day|days|hour|hours) ago$/
     */
    public function theyAbandonedTheirCart(OrderInterface $cart, $amount, $time)
    {
        $cart->setUpdatedAt(new \DateTime('-'.$amount.' '.$time));
        $this->orderManager->flush();
    }

    /**
     * @Then /^(this cart) should be automatically deleted$/
     */
    public function thisCartShouldBeAutomaticallyDeleted(OrderInterface $cart)
    {
        $this->expiredCartsRemover->remove();

        Assert::null(
            $cart->getId(),
            'This cart should not exist in registry but it does.'
        );
    }

    /**
     * @Then /^(this cart) should not be deleted$/
     */
    public function thisCartShouldNotBeDeleted(OrderInterface $cart)
    {
        $this->expiredCartsRemover->remove();

        Assert::notNull(
            $cart->getId(),
            'This cart should be in registry but it is not.'
        );
    }
}
