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

namespace Sylius\Bundle\ApiBundle\Assigner;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/** @experimental */
class CartToUserAssigner implements CartToUserAssignerInterface
{
    /** @var ObjectManager */
    private $orderManager;

    /** @var SessionInterface */
    private $session;

    /** @var OrderRepositoryInterface */
    private $cartRepository;

    public function __construct(ObjectManager $orderManager, SessionInterface $session, OrderRepositoryInterface $cartRepository){
        $this->orderManager = $orderManager;
        $this->session = $session;
        $this->cartRepository = $cartRepository;
    }

    public function assignByCustomer(CustomerInterface $customer): void
    {
        if ($this->session->has('cart_token')) {
            $cartToken = $this->session->get('cart_token');
            /** @var OrderInterface $cart */
            $cart = $this->cartRepository->findCartByTokenValue($cartToken);

            $cart->setCustomer($customer);
            $this->orderManager->persist($cart);
        }
    }
}
