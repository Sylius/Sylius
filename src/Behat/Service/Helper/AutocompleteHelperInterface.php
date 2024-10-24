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

namespace Sylius\Behat\Service\Helper;

use Behat\Mink\Driver\DriverInterface;

interface AutocompleteHelperInterface
{
    public function search(DriverInterface $driver, string $selector, string $searchString): mixed;

    /**
     * @return array<string>
     */
    public function getSelectedItems(DriverInterface $driver, string $selector): array;

    public function selectByName(DriverInterface $driver, string $selector, string $name): void;

    public function removeByName(DriverInterface $driver, string $selector, string $name): void;

    public function selectByValue(DriverInterface $driver, string $selector, string $value): void;

    public function removeByValue(DriverInterface $driver, string $selector, string $value): void;

    public function clear(DriverInterface $driver, string $selector): void;
}
