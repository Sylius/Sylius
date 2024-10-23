<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Service\Factory;

use Sylius\Component\Core\Factory\AddressFactoryInterface as BaseAddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;

/**
 * @implements BaseAddressFactoryInterface<AddressInterface>
 */
interface AddressFactoryInterface extends BaseAddressFactoryInterface
{
    public function createDefault(): AddressInterface;

    public function createDefaultWithCountryCode(string $countryCode): AddressInterface;
}
