<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Promotion\Provider;

use Sylius\Promotion\Model\PromotionInterface;
use Sylius\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface PreQualifiedPromotionsProviderInterface
{
    /**
     * @param PromotionSubjectInterface $subject
     *
     * @return PromotionInterface[]
     */
    public function getPromotions(PromotionSubjectInterface $subject);
}
