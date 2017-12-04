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

namespace Sylius\Component\Registry;

interface ServiceRegistryInterface
{
    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param string $identifier
     * @param object $service
     *
     * @throws ExistingServiceException
     * @throws \InvalidArgumentException
     */
    public function register(string $identifier, $service): void;

    /**
     * @param string $identifier
     *
     * @throws NonExistingServiceException
     */
    public function unregister(string $identifier): void;

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has(string $identifier): bool;

    /**
     * @param string $identifier
     *
     * @return object
     *
     * @throws NonExistingServiceException
     */
    public function get(string $identifier);
}
