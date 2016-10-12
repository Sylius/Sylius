<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Provider;

use Sylius\Component\Addressing\Model\AddressInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface ProvinceNamingProviderInterface
{
    /**
     * @param AddressInterface $address
     *
     * @return string
     */
    public function getName(AddressInterface $address);

    /**
     * @param AddressInterface $address
     *
     * @return string
     */
    public function getAbbreviation(AddressInterface $address);
}
