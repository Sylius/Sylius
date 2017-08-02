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
final class ListenerNotFoundException extends \InvalidArgumentException
{
    /**
     * @param string $name
     * @param \Exception|null $previous
     */
    public function __construct($name, \Exception $previous = null)
    {
        parent::__construct(sprintf('Listener with name "%s" could not be found!', $name), 0, $previous);
    }
}
