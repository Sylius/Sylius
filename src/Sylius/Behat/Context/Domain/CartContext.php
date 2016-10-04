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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
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
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Given /^(?:|he|she) abandoned (the cart) ([^"]+) (hours|minutes|seconds) ago$/
     */
    public function heAbandonedHisCartHoursAgo(OrderInterface $cart, $amount, $time)
    {
        $cart->setUpdatedAt(new \DateTime('-'.$amount.' '.$time));
    }

    /**
     * @Then /^(this cart) should be deleted from registry$/
     */
    public function thisCartShouldBeDeletedFromRegistry(OrderInterface $cart)
    {
        $cart = $this->orderRepository->find($cart->getId());
        Assert::null(
            $cart,
            'This cart should not exist in registry but it does.'
        );
    }
}
