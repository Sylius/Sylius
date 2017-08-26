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

namespace Sylius\Bundle\FixturesBundle\Fixture;

use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class FixtureRegistry implements FixtureRegistryInterface
{
    /**
     * @var array
     */
    private $fixtures = [];

    /**
     * @param FixtureInterface $fixture
     */
    public function addFixture(FixtureInterface $fixture)
    {
        Assert::keyNotExists($this->fixtures, $fixture->getName(), 'Fixture with name "%s" is already registered.');

        $this->fixtures[$fixture->getName()] = $fixture;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixture($name)
    {
        if (!isset($this->fixtures[$name])) {
            throw new FixtureNotFoundException($name);
        }

        return $this->fixtures[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }
}
