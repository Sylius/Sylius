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

namespace Sylius\Component\Core\Model;

interface URLRedirectInterface
{
    /**
     * @return int
     */
    public function getId(): ?int;

    /**
     * @return string
     */
    public function getOldRoute(): string;

    /**
     * @param string $oldRoute
     */
    public function setOldRoute(string $oldRoute): void;

    /**
     * @return string
     */
    public function getNewRoute(): string;

    /**
     * @param string $newRoute
     */
    public function setNewRoute(string $newRoute): void;

    /**
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * Sets the type of the redirect (see class constants for values)
     *
     * @param string|null $type
     */
    public function setType(?string $type): void;
}
