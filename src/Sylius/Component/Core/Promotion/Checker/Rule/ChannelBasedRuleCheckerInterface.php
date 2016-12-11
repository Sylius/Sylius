<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Component\Core\Promotion\ChannelBasedPromotionInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ChannelBasedRuleCheckerInterface extends RuleCheckerInterface, ChannelBasedPromotionInterface
{
}
