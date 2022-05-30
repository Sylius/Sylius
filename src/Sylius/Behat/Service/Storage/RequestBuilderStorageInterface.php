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

namespace Sylius\Behat\Service\Storage;

use Sylius\Behat\Client\RequestBuilder;

interface RequestBuilderStorageInterface
{
    public function get(): ?RequestBuilder;
    public function set(RequestBuilder $requestBuilder): void;
}
