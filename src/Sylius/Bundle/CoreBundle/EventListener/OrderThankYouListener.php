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

use Sylius\Bundle\CoreBundle\Event\OrderCompleteEvent;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class OrderThankYouListener
{
    /**
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $redirectTo;

    public function __construct(CartProviderInterface $cartProvider, UrlGeneratorInterface $router, $redirectTo)
    {
        $this->cartProvider = $cartProvider;
        $this->router = $router;
        $this->redirectTo = $redirectTo;
    }

    public function abandonCart(OrderCompleteEvent $event)
    {
        if (!in_array(
            $event->getSubject()->getState(),
            array(
                PaymentInterface::STATE_PENDING,
                PaymentInterface::STATE_PROCESSING,
                PaymentInterface::STATE_COMPLETED
            )
        )
        ) {
            $this->cartProvider->abandonCart();

            return;
        }

        $event->setResponse(
            new RedirectResponse(
                $this->router->generate($this->redirectTo)
            )
        );
    }
}
