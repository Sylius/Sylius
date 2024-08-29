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

namespace Sylius\Behat\Page\Shop;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface as BasePageInterface;

interface PageInterface extends BasePageInterface
{
    /**
     * @param array<string, string> $parameters
     */
    public function fillElement(string $value, string $element, array $parameters = []): void;

    /**
     * @param array<string, string> $parameters
     */
    public function getValidationMessage(string $element, array $parameters = []): string;
}
