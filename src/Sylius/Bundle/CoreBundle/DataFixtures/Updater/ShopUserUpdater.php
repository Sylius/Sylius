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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Webmozart\Assert\Assert;

final class ShopUserUpdater implements ShopUserUpdaterInterface
{
    public function __construct(private CustomerUpdaterInterface $customerUpdater)
    {
    }

    public function update(ShopUserInterface $shopUser, array $attributes): void
    {
        /** @var CustomerInterface $customer */
        $customer = $shopUser->getCustomer();

        Assert::isInstanceOf($customer, CustomerInterface::class);

        $this->customerUpdater->update($customer, $attributes);

        $shopUser->setPlainPassword($attributes['password']);
        $shopUser->setEnabled($attributes['enabled']);
        $shopUser->addRole('ROLE_USER');
    }
}
