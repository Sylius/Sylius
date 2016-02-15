<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Provider;

use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface PreQualifiedPromotionsProviderInterface
{
    /**
     * @return PromotionInterface[]
     */
    public function provide();
}
