<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Checker;

use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;

/**
 * Promotion rule checker interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RuleCheckerInterface
{
    /**
     * @param PromotionSubjectInterface $subject
     * @param array                     $configuration
     *
     * @return Boolean
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration);

    /**
     * @return string
     */
    public function getConfigurationFormType();
}
