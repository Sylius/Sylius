<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Checker;

use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DatesEligibilityChecker implements PromotionEligibilityCheckerInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionInterface $promotion)
    {
        $now = new \DateTime();

        $startsAt = $promotion->getStartsAt();
        if (null !== $startsAt && $now < $startsAt) {
            return false;
        }

        $endsAt = $promotion->getEndsAt();
        if (null !== $endsAt && $now > $endsAt) {
            return false;
        }

        return true;
    }
}
