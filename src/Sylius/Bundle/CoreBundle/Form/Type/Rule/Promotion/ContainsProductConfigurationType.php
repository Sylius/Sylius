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

use Sylius\Bundle\CoreBundle\Form\Type\Rule\ContainsProductConfigurationType as BaseContainsProductConfigurationType;

/**
 * Contains product rule configuration form type.
 *
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class ContainsProductConfigurationType extends BaseContainsProductConfigurationType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_contains_product_configuration';
    }
}
