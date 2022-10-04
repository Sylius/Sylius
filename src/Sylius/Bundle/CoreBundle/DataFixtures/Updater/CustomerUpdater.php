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

final class CustomerUpdater implements CustomerUpdaterInterface
{
    public function update(CustomerInterface $customer, array $attributes): void
    {
        $customer->setEmail($attributes['email']);
        $customer->setFirstName($attributes['first_name']);
        $customer->setLastName($attributes['last_name']);
        $customer->setGroup($attributes['customer_group']);
        $customer->setGender($attributes['gender']);
        $customer->setPhoneNumber($attributes['phone_number']);
        $customer->setBirthday($attributes['birthday']);
    }
}
