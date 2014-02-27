<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Promotion\Checker;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Model\OrderItemInterface;
use Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\ResourceBundle\Exception\UnexpectedTypeException;

/**
 * Checks if order contains selected products.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ContainItemRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Bundle\CoreBundle\Model\OrderInterface');
        }

        $variantCount = $configuration['variants']->count();
        if ($configuration['only'] && $subject->getItems()->count() !== $variantCount) {
            return false;
        }

        /* @var $item OrderItemInterface */
        $contains = array();
        foreach ($subject->getItems() as $item) {
            if ($configuration['variants']->contains($item->getVariant()->getId())) {
                $contains[] = true;
            }
        }

        $match = count($contains) === $variantCount;

        return $configuration['exclude'] ? !$match : $match;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_contain_item_configuration';
    }
}
