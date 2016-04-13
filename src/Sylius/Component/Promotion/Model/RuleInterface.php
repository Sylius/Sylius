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
interface RuleInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);

    /**
     * @return PromotionInterface
     */
    public function getPromotion();

    /**
     * @param PromotionInterface $promotion
     */
    public function setPromotion(PromotionInterface $promotion = null);
}
