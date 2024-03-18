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

namespace Sylius\Behat\Client;

interface RequestFactoryInterface
{
    public function index(
        ?string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface;

    /**
     * @param array<string, mixed> $queryParameters
     */
    public function subResourceIndex(
        string $section,
        string $resource,
        string $id,
        string $subResource,
        array $queryParameters = [],
    ): RequestInterface;

    public function show(
        string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface;

    public function create(
        string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface;

    public function update(
        string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface;

    public function delete(
        string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface;

    public function upload(
        string $section,
        string $resource,
        array $files,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface;

    public function transition(
        string $section,
        string $resource,
        string $id,
        string $transition,
    ): RequestInterface;

    public function customItemAction(
        string $section,
        string $resource,
        string $id,
        string $type,
        string $action,
    ): RequestInterface;

    public function custom(
        string $url,
        string $method,
        array $additionalHeaders = [],
        ?string $token = null,
    ): RequestInterface;
}
