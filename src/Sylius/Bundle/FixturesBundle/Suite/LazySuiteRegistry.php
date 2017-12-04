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

namespace Sylius\Bundle\FixturesBundle\Suite;

final class LazySuiteRegistry implements SuiteRegistryInterface
{
    /**
     * @var SuiteFactoryInterface
     */
    private $suiteFactory;

    /**
     * @var array
     */
    private $suiteDefinitions = [];

    /**
     * @var array
     */
    private $suites = [];

    /**
     * @param SuiteFactoryInterface $suiteFactory
     */
    public function __construct(SuiteFactoryInterface $suiteFactory)
    {
        $this->suiteFactory = $suiteFactory;
    }

    /**
     * @param string $name
     * @param array $configuration
     */
    public function addSuite(string $name, array $configuration): void
    {
        $this->suiteDefinitions[$name] = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuite(string $name): SuiteInterface
    {
        if (isset($this->suites[$name])) {
            return $this->suites[$name];
        }

        if (!isset($this->suiteDefinitions[$name])) {
            throw new SuiteNotFoundException($name);
        }

        return $this->suites[$name] = $this->suiteFactory->createSuite($name, $this->suiteDefinitions[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSuites(): array
    {
        $suites = [];
        foreach (array_keys($this->suiteDefinitions) as $name) {
            $suites[$name] = $this->getSuite($name);
        }

        return $suites;
    }
}
