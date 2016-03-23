<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\MultiContainerExtension\Context\Environment\Handler;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Environment\Exception\EnvironmentIsolationException;
use Behat\Testwork\Environment\Handler\EnvironmentHandler;
use Behat\Testwork\Suite\Exception\SuiteConfigurationException;
use Behat\Testwork\Suite\Suite;
use Sylius\Behat\Extension\MultiContainerExtension\Context\Environment\UninitializedContextServiceEnvironment;
use Sylius\Behat\Extension\MultiContainerExtension\ContextRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ContextServiceEnvironmentHandler implements EnvironmentHandler
{
    /**
     * @var ContextRegistry
     */
    private $contextRegistry;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContextRegistry $contextRegistry
     * @param ContainerInterface $container
     */
    public function __construct(ContextRegistry $contextRegistry, ContainerInterface $container)
    {
        $this->contextRegistry = $contextRegistry;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsSuite(Suite $suite)
    {
        return $suite->hasSetting('contexts_as_services');
    }

    /**
     * {@inheritdoc}
     */
    public function buildEnvironment(Suite $suite)
    {
        $environment = new UninitializedContextServiceEnvironment($suite);
        foreach ($this->getSuiteContexts($suite) as $serviceId) {
            $environment->registerContextClass($serviceId, $this->contextRegistry->getClass($serviceId));
        }

        return $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEnvironmentAndSubject(Environment $environment, $testSubject = null)
    {
        return $environment instanceof UninitializedContextServiceEnvironment;
    }

    /**
     * {@inheritdoc}
     */
    public function isolateEnvironment(Environment $uninitializedEnvironment, $testSubject = null)
    {
        if (!$uninitializedEnvironment instanceof UninitializedContextServiceEnvironment) {
            throw new EnvironmentIsolationException(sprintf(
                'ContextServiceEnvironmentHandler does not support isolation of `%s` environment.',
                get_class($uninitializedEnvironment)
            ), $uninitializedEnvironment);
        }

        if (!$this->container->isScopeActive('scenario')) {
            $this->container->enterScope('scenario');
        }

        $environment = new InitializedContextEnvironment($uninitializedEnvironment->getSuite());
        foreach ($uninitializedEnvironment->getContextsServicesIds() as $serviceId) {
            /** @var Context $context */
            $context = $this->container->get($serviceId);
            $environment->registerContext($context);
        }

        return $environment;
    }

    /**
     * @param Suite $suite
     *
     * @return string[]
     *
     * @throws SuiteConfigurationException If `contexts_as_services` setting is not an array
     */
    private function getSuiteContexts(Suite $suite)
    {
        if (!is_array($suite->getSetting('contexts_as_services'))) {
            throw new SuiteConfigurationException(
                sprintf('`contexts_as_services` setting of the "%s" suite is expected to be an array, %s given.',
                    $suite->getName(),
                    gettype($suite->getSetting('contexts_as_services'))
                ),
                $suite->getName()
            );
        }

        return $suite->getSetting('contexts_as_services');
    }
}
