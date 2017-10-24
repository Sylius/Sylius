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

interface PrioritizedServiceRegistryInterface
{
    /**
     * @return iterable
     */
    public function all(): iterable;

    /**
     * @param object $service
     * @param int $priority
     *
     * @throws ExistingServiceException
     * @throws \InvalidArgumentException
     */
    public function register($service, int $priority = 0): void;

    /**
     * @param object $service
     *
     * @throws NonExistingServiceException
     */
    public function unregister($service): void;

    /**
     * @param object $service
     *
     * @return bool
     */
    public function has($service): bool;
}
