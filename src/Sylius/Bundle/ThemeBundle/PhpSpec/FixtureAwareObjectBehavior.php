<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle\PhpSpec;

use PhpSpec\ObjectBehavior;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class FixtureAwareObjectBehavior extends ObjectBehavior
{
    /**
     * @param string $fixturePath
     *
     * @return string
     */
    protected function getFixturePath($fixturePath)
    {
        $path = __DIR__ . '/../spec/fixtures/' . $fixturePath;

        if (false === $realpath = realpath($path)) {
            throw new \RuntimeException(sprintf('Fixture "%s" does not exist!', $path));
        }

        return $realpath;
    }
}
