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

namespace Sylius\Behat\Service;

interface ResponseLoaderInterface
{
    public function getMockedResponse(string $source): array;

    public function getExpectedResponse(string $source): array;
}
