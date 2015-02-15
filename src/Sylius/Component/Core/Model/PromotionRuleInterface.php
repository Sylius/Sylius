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

use Sylius\Component\Promotion\Model\RuleInterface;

interface PromotionRuleInterface extends RuleInterface
{
    const TYPE_NTH_ORDER        = 'nth_order';
    const TYPE_SHIPPING_COUNTRY = 'shipping_country';
    const TYPE_TAXONOMY         = 'taxonomy';
    const TYPE_USER_GROUP       = 'user_group';
}
