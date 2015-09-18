<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * checks if order contains nth times the same product
 *
 * @author Bruno Roux <bruno@yproximite.com>
 */
class NthProductRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        $productQuantities = [];
        foreach ($subject->getItems() as $item) {
            $productId       = $item->getVariant()->getObject()->getId();
            $productQuantity = $item->getQuantity();

            if (array_key_exists($productId, $productQuantities)) {
                $productQuantities[$productId] += $productQuantity;
            } else {
                $productQuantities[$productId] = $productQuantity;
            }
        }

        foreach ($productQuantities as $productQuantity) {
            if ($productQuantity >= $configuration['nth']) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_nth_product_configuration';
    }
}
