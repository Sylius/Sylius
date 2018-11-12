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

namespace Sylius\Behat\Page\Shop\Account\AddressBook;

use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\AddressInterface;

interface CreatePageInterface extends SymfonyPageInterface
{
    public function fillAddressData(AddressInterface $address);

    public function selectCountry(string $name);

    public function addAddress();

    public function hasProvinceValidationMessage(): bool;

    public function countValidationMessages(): int;
}
