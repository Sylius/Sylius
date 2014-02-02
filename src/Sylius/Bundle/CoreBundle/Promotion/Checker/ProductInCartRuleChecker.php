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
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface;
use Sylius\Bundle\ResourceBundle\Exception\UnexpectedTypeException;

/**
 * Checks if a specified product is in the cart
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ProductInCartRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Bundle\CoreBundle\Model\OrderInterface');
        }

        foreach ($subject->getItems() as $item) {
            if ($item->getProduct()->getId() == $configuration['product']) {
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
        return 'sylius_promotion_rule_product_in_cart_configuration';
    }
}
