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
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * Checks if order contains selected product.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ContainProductRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        if ($configuration['only'] && $subject->getItems()->count() !== 1) {
            return false;
        }

        /** @var $item OrderItemInterface */
        foreach ($subject->getItems() as $item) {
            if ($item->getProduct()->getId() === $configuration['product']) {
                return !$configuration['exclude'];
            }
        }

        return (Boolean) $configuration['exclude'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_contain_product_configuration';
    }
}
