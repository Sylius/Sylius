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

namespace Sylius\Behat\Client;

interface RequestBuilderFactoryInterface
{
    public function show(
        string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null
    ): RequestBuilder;

    public function create(
        string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null
    ): RequestBuilder;

    public function update(
        string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null
    ): RequestBuilder;
}
