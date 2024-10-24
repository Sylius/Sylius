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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleCheckerInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class ProductDeletionListener
{
    public function __construct(private ProductInPromotionRuleCheckerInterface $productInPromotionRuleChecker)
    {
    }

    public function protectFromRemovingProductInUseByPromotionRule(GenericEvent $event): void
    {
        $product = $event->getSubject();
        Assert::isInstanceOf($product, ProductInterface::class);

        if ($this->productInPromotionRuleChecker->isInUse($product)) {
            $event->setMessageType('error');
            $event->setMessage('sylius.product.in_use_by_promotion_rule');
            $event->stopPropagation();
        }
    }
}
