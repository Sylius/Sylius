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

namespace Sylius\Bundle\FixturesBundle\Listener;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface ListenerRegistryInterface
{
    /**
     * @param string $name
     *
     * @return ListenerInterface
     *
     * @throws ListenerNotFoundException
     */
    public function getListener($name);

    /**
     * @return ListenerInterface[] Name indexed
     */
    public function getListeners();
}
