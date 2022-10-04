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

namespace Sylius\Bundle\CoreBundle\DataFixtures\DefaultValues;

use Faker\Generator;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerGroupFactoryInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

final class ShopUserDefaultValues implements ShopUserDefaultValuesInterface
{
    public function __construct(private CustomerDefaultValuesInterface $customerDefaultValues)
    {
    }

    public function getDefaults(Generator $faker): array
    {
        return array_merge($this->customerDefaultValues->getDefaults($faker), [
            'enabled' => true,
            'password' => 'password123',
        ]);
    }
}
