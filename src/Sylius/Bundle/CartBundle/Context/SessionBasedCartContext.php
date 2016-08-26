<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Context;

use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Context\CartNotFoundException;
use Sylius\Component\Cart\Repository\CartRepositoryInterface;
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
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param SessionInterface $session
     * @param string $sessionKeyName
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(SessionInterface $session, $sessionKeyName, CartRepositoryInterface $cartRepository)
    {
        $this->session = $session;
        $this->sessionKeyName = $sessionKeyName;
        $this->cartRepository = $cartRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        if (!$this->session->has($this->sessionKeyName)) {
            throw new CartNotFoundException('Sylius was not able to find the cart in session');
        }

        $cart = $this->cartRepository->findCartById($this->session->get($this->sessionKeyName));

        if (null === $cart) {
            $this->session->remove($this->sessionKeyName);

            throw new CartNotFoundException('Sylius was not able to find the cart in session');
        }

        return $cart;
    }
}
