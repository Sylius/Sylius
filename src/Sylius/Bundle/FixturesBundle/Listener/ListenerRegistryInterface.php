<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Listener;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
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
