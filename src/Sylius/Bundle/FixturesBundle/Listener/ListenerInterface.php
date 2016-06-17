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

use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface ListenerInterface extends ConfigurationInterface
{
    /**
     * @return string
     */
    public function getName();
}
