<?php

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