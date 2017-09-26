<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Provider;

use Sylius\Component\Core\Model\CustomerGroupInterface;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Provider\CustomerTaxCategoryProviderInterface;

final class DefaultCustomerTaxCategoryProvider implements CustomerTaxCategoryProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCustomerTaxCategory(OrderInterface $order): ?CustomerTaxCategoryInterface
    {
        $customerTaxCategory = null;
        $customer = $order->getCustomer();
        if (null !== $customer) {
            /** @var CustomerGroupInterface $customerGroup */
            $customerGroup = $customer->getGroup();
            if (null !== $customerGroup) {
                $customerTaxCategory = $customerGroup->getTaxCategory();
            }
        }

        return $customerTaxCategory ?: $order->getChannel()->getDefaultCustomerTaxCategory();
    }
}
