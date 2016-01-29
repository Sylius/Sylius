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
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Promotion\Checker\ItemCountRuleChecker;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * Checks if order contains the given variant.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ContainsProductRuleChecker extends ItemCountRuleChecker
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
            if ($configuration['variant'] == $item->getVariant()->getId()) {
                if (!$configuration['exclude']) {
                    if (isset($configuration['count'])) {
                        return parent::isEligible($item, $configuration);
                    }

                    return true;
                }

                return false;
            }
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
}
