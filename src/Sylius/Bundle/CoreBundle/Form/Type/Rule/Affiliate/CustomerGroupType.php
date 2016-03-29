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

use Sylius\Bundle\CoreBundle\Form\Type\Rule\CustomerGroupType as BaseCustomerGroupType;

/**
 * @author Antonio Perić <antonio@locastic.com>
 */
class CustomerGroupType extends BaseCustomerGroupType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_affiliate_rule_customer_group_configuration';
    }
}
