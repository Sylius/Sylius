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
}