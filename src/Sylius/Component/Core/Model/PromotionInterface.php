<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Promotion\Model\PromotionInterface as BasePromotionInterface;

/**
 * @author Kristian Loevstroem <kristian@loevstroem.dk>
 */
interface PromotionInterface extends BasePromotionInterface, ChannelsAwareInterface
{
}
