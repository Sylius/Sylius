<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Behat\Testwork\Environment;

use Behat\Behat\Context\Environment\ContextEnvironment;
use Behat\Testwork\Call\Callee;
use Behat\Testwork\Environment\Exception\EnvironmentBuildException;
use Behat\Testwork\Environment\Exception\EnvironmentIsolationException;
use Behat\Testwork\Environment\Handler\EnvironmentHandler;
use Behat\Testwork\Environment\Reader\EnvironmentReader;
use Behat\Testwork\Suite\Suite;


/**
 * Builds, isolates and reads environments using registered handlers and readers.
 *
 * Duplicated from the Behat project to store callees into a local var
 */
final class EnvironmentManager
{
    /** @var EnvironmentHandler[] */
    private array $handlers = [];

    /** @var EnvironmentReader[] */
    private array $readers = [];

    /** @var array<string, Callee[]> */
    private array $callees = [];

    /**
     * Registers environment handler.
     */
    public function registerEnvironmentHandler(EnvironmentHandler $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * Registers environment reader.
     */
    public function registerEnvironmentReader(EnvironmentReader $reader): void
    {
        $this->readers[] = $reader;
    }

    /**
     * Builds new environment for provided test suite.
     *
     * @throws EnvironmentBuildException
     */
    public function buildEnvironment(Suite $suite): Environment
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supportsSuite($suite)) {
                return $handler->buildEnvironment($suite);
            }
        }

        throw new EnvironmentBuildException(sprintf('None of the registered environment handlers seem to support `%s` suite.', $suite->getName()), $suite);
    }

    /**
     * Creates new isolated test environment using built one.
     *
     * @throws EnvironmentIsolationException If appropriate environment handler is not found
     */
    public function isolateEnvironment(Environment $environment, mixed $testSubject = null): Environment
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supportsEnvironmentAndSubject($environment, $testSubject)) {
                return $handler->isolateEnvironment($environment, $testSubject);
            }
        }

        throw new EnvironmentIsolationException(sprintf('None of the registered environment handlers seem to support `%s` environment.', $environment::class), $environment, $testSubject);
    }

    /**
     * Reads all callees from environment using registered environment readers.
     *
     * @return Callee[]
     */
    public function readEnvironmentCallees(Environment $environment): array
    {
        $localKey = $environment->getSuite()->getName();

        if ($environment instanceof ContextEnvironment) {
            $localKey.= serialize($environment->getContextClasses());
        }

        if (isset($this->callees[$localKey])) {
            return $this->callees[$localKey];
        }
        $callees = [];

        foreach ($this->readers as $reader) {
            if ($reader->supportsEnvironment($environment)) {
                $callees = array_merge($callees, $reader->readEnvironmentCallees($environment));
            }
        }

        $this->callees[$localKey] = $callees;

        return $callees;
    }
}
