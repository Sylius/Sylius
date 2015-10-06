<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule\Promotion;

use Sylius\Bundle\CoreBundle\Form\Type\Rule\NthOrderConfigurationType as BaseNthOrderConfigurationType;

/**
 * Nth order rule configuration form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class NthOrderConfigurationType extends BaseNthOrderConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_nth_order_configuration';
    }
}
