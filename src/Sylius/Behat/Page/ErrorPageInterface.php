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

namespace Sylius\Behat\Page;

use Behat\Mink\Exception\ElementNotFoundException;

interface ErrorPageInterface
{
    public function getCode(): int;

    /**
     * @throws ElementNotFoundException
     */
    public function getTitle(): string;
}
