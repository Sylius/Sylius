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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Webmozart\Assert\Assert;

final class UserCartRecalculationListener
{
    public function __construct(
        private CartContextInterface $cartContext,
        private OrderProcessorInterface $orderProcessor,
        private SectionProviderInterface $uriBasedSectionContext,
    ) {
    }

    /**
     * @param InteractiveLoginEvent|UserEvent $event
     */
    public function recalculateCartWhileLogin(object $event): void
    {
        if (!$this->uriBasedSectionContext->getSection() instanceof ShopSection) {
            return;
        }

        /** @psalm-suppress DocblockTypeContradiction */
        if (!$event instanceof InteractiveLoginEvent && !$event instanceof UserEvent) {
            throw new \TypeError(sprintf(
                '$event needs to be an instance of "%s" or "%s"',
                InteractiveLoginEvent::class,
                UserEvent::class,
            ));
        }

        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException) {
            return;
        }

        Assert::isInstanceOf($cart, OrderInterface::class);

        $this->orderProcessor->process($cart);
    }
}
