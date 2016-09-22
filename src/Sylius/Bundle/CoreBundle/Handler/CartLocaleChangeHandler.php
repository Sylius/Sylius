<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Locale\Handler\LocaleChangeHandlerInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CartLocaleChangeHandler implements LocaleChangeHandlerInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var ObjectManager
     */
    private $cartManager;

    /**
     * @param CartContextInterface $cartContext
     * @param ObjectManager $cartManager
     */
    public function __construct(CartContextInterface $cartContext, ObjectManager $cartManager)
    {
        $this->cartContext = $cartContext;
        $this->cartManager = $cartManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($code)
    {
        try {
            /** @var OrderInterface $cart */
            $cart = $this->cartContext->getCart();
            $cart->setLocaleCode($code);

            $this->cartManager->persist($cart);
            $this->cartManager->flush();
        } catch (CartNotFoundException $exception) {
            throw new HandleException(self::class, 'Sylius was unable to find the cart.', $exception);
        }
    }
}
