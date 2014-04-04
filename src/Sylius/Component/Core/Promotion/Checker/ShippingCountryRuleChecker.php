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

use Sylius\Bundle\ResourceBundle\Exception\UnexpectedTypeException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * Checks if shipping country match.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingCountryRuleChecker implements RuleCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        if (null === $address = $subject->getShippingAddress()) {
            return false;
        }

        return $address->getCountry()->getId() === $configuration['country'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_shipping_country_configuration';
    }
}
