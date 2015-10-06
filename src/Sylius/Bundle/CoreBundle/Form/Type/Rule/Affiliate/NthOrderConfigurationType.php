<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule\Affiliate;

use Sylius\Bundle\CoreBundle\Form\Type\Rule\NthOrderConfigurationType as BaseNthOrderConfigurationType;

/**
 * Nth order affiliate rule configuration form type.
 *
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class NthOrderConfigurationType extends BaseNthOrderConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_affiliate_rule_nth_order_configuration';
    }
}
