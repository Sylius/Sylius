<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Fixture;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixtureNotFoundException extends \InvalidArgumentException
{
    /**
     * @param string $name
     * @param \Exception|null $previous
     */
    public function __construct($name, \Exception $previous = null)
    {
        parent::__construct(sprintf('Fixture with name "%s" could not be found!', $name), 0, $previous);
    }
}
