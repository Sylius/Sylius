<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 29/01/18
 * Time: 10:57
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
     * @param null|string $type
     */
    public function setType(?string $type): void;
}
