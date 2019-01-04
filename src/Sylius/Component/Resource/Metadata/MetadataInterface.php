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

namespace Sylius\Component\Resource\Metadata;

interface MetadataInterface
{
    public function getAlias(): string;

    public function getApplicationName(): string;

    public function getName(): string;

    public function getHumanizedName(): string;

    public function getPluralName(): string;

    public function getDriver(): string;

    /**
     * @return ?string
     */
    public function getTemplatesNamespace(): ?string;

    /**
     * @return string|array
     *
     * @throws \InvalidArgumentException
     */
    public function getParameter(string $name);

    /**
     * Return all the metadata parameters.
     */
    public function getParameters(): array;

    public function hasParameter(string $name): bool;

    /**
     * @throws \InvalidArgumentException
     */
    public function getClass(string $name): string;

    public function hasClass(string $name): bool;

    public function getServiceId(string $serviceName): string;

    public function getPermissionCode(string $permissionName): string;
}
