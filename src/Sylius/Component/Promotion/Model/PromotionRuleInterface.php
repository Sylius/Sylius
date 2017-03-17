<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionRuleInterface extends ResourceInterface, ConfigurablePromotionElementInterface
{
    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);

    /**
     * @param PromotionInterface $promotion
     */
    public function setPromotion(PromotionInterface $promotion = null);
}
