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
interface ConfigurablePromotionElementInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @return PromotionInterface
     */
    public function getPromotion();
}
