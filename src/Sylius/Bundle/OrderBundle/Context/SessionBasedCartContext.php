<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Context;

use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SessionBasedCartContext implements CartContextInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $sessionKeyName;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param SessionInterface $session
     * @param string $sessionKeyName
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(SessionInterface $session, $sessionKeyName, OrderRepositoryInterface $orderRepository)
    {
        $this->session = $session;
        $this->sessionKeyName = $sessionKeyName;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        if (!$this->session->has($this->sessionKeyName)) {
            throw new CartNotFoundException('Sylius was not able to find the cart in session');
        }

        $cart = $this->orderRepository->findCartById($this->session->get($this->sessionKeyName));

        if (null === $cart) {
            $this->session->remove($this->sessionKeyName);

            throw new CartNotFoundException('Sylius was not able to find the cart in session');
        }

        return $cart;
    }
}
