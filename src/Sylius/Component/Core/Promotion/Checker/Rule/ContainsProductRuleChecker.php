<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ContainsProductRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        /* @var $item OrderItemInterface */
        foreach ($subject->getItems() as $item) {
            if ($configuration['variant'] != $item->getVariant()->getId()) {
                continue;
            }

            return $this->isItemEligible($item, $configuration);
        }

        return (bool) $configuration['exclude'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_contains_product_configuration';
    }

    /**
     * @param OrderItemInterface $item
     * @param array $configuration
     *
     * @return bool
     */
    private function isItemEligible(OrderItemInterface $item, array $configuration)
    {
        if (!$configuration['exclude']) {
            if (isset($configuration['count'])) {
                return $this->isItemQuantityEligible($item->getQuantity(), $configuration);
            }

            return true;
        }

        return false;
    }

    /**
     * @param int $quantity
     * @param array $configuration
     *
     * @return bool
     */
    private function isItemQuantityEligible($quantity, array $configuration)
    {
        return $quantity >= $configuration['count'];
    }
}
