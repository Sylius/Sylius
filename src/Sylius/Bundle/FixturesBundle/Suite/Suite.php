<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Suite;

use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Zend\Stdlib\SplPriorityQueue;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class Suite implements SuiteInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var SplPriorityQueue
     */
    private $fixtures;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->fixtures = new SplPriorityQueue();
    }

    /**
     * @param FixtureInterface $fixture
     * @param array $options
     * @param int $priority
     */
    public function addFixture(FixtureInterface $fixture, array $options, $priority = 0)
    {
        $this->fixtures->insert(['fixture' => $fixture, 'options' => $options], $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        foreach ($this->fixtures as $fixture) {
            yield $fixture['fixture'] => $fixture['options'];
        }
    }
}
