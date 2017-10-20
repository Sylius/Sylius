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

namespace Sylius\Component\Addressing\Provider;

use Sylius\Component\Addressing\Model\AddressInterface;

interface ProvinceNamingProviderInterface
{
    /**
     * @param AddressInterface $address
     *
     * @return string
     */
    public function getName(AddressInterface $address): string;

    /**
     * @param AddressInterface $address
     *
     * @return string
     */
    public function getAbbreviation(AddressInterface $address): string;
}
