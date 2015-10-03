<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * Class NthProductFixedDiscountAction
 *
 * @author Bruno Roux <bruno@yproxmite.com>
 */
class NthProductFixedDiscountAction extends DiscountAction
{
    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        $nth              = $configuration['nth'];
        $amount           = $configuration['amount'];
        $productQuantites = [];

        foreach ($subject->getItems() as $item) {
            $product   = $item->getProduct();
            $productId = $product->getId();

            if (!array_key_exists($productId, $productQuantites)) {
                $productQuantites[$productId] = $item->getQuantity();
            } else {
                $productQuantites[$productId] += $item->getQuantity();
            }
        }

        foreach ($productQuantites as $productQuantity) {
            if ($productQuantity >= $nth) {
                $adjustment = $this->createAdjustment($promotion);

                $applyXTimes = floor($productQuantity / $nth);
                $adjustment->setAmount((int) -$applyXTimes*$amount);

                $subject->addAdjustment($adjustment);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_nth_product_fixed_discount_configuration';
    }
}
