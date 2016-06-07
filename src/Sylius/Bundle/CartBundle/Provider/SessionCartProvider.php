<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Provider;

use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\Repository\CartRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SessionCartProvider implements CartProviderInterface
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
    public function __construct(
        SessionInterface $session,
        $sessionKeyName,
        CartRepositoryInterface $cartRepository
    ) {
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
            return null;
        }

        $cart = $this->cartRepository->findCartById($this->session->get($this->sessionKeyName));

        if (null === $cart) {
            $this->session->remove($this->sessionKeyName);
        }

        return $cart;
    }
}
