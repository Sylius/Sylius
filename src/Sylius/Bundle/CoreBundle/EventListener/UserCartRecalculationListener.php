<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserCartRecalculationListener
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var OrderRecalculatorInterface
     */
    private $orderRecalculator;

    /**
     * @param CartContextInterface $cartContext
     * @param OrderRecalculatorInterface $orderRecalculator
     */
    public function __construct(CartContextInterface $cartContext, OrderRecalculatorInterface $orderRecalculator)
    {
        $this->cartContext = $cartContext;
        $this->orderRecalculator = $orderRecalculator;
    }

    /**
     * @param Event $event
     *
     * @throws UnexpectedTypeException
     */
    public function recalculateCartWhileLogin(Event $event)
    {
        $cart = $this->cartContext->getCart();

        if (!$cart instanceof OrderInterface) {
            throw new UnexpectedTypeException($cart, OrderInterface::class);
        }

        $this->orderRecalculator->recalculate($cart);
    }
}
