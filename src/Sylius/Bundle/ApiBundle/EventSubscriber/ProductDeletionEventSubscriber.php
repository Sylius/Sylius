<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Sylius\Bundle\ApiBundle\Exception\ProductCannotBeRemoved;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleCheckerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ProductDeletionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private ProductInPromotionRuleCheckerInterface $productInPromotionRuleChecker)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['protectFromRemovingProductInUseByPromotionRule', EventPriorities::PRE_WRITE],
        ];
    }

    public function protectFromRemovingProductInUseByPromotionRule(ViewEvent $event): void
    {
        $product = $event->getControllerResult();

        if (!$product instanceof ProductInterface || $event->getRequest()->getMethod() !== 'DELETE') {
            return;
        }

        if ($this->productInPromotionRuleChecker->isInUse($product)) {
            throw new ProductCannotBeRemoved('Cannot delete a product that is in use by a promotion rule.');
        }
    }
}
