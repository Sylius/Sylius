<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Manager;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder;

/**
 * Settings manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SettingsManager implements SettingsManagerInterface
{
    /**
     * Schema registry.
     *
     * @var SchemaRegistryInterface
     */
    protected $schemaRegistry;

    /**
     * Object manager.
     *
     * @var ObjectManager
     */
    protected $parameterManager;

    /**
     * Parameter object repository.
     *
     * @var RepositoryInterface
     */
    protected $parameterRepository;

    /**
     * Cache.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Runtime cache for resolved parameters
     *
     * @var array
     */
    protected $resolvedSettings = array();

    /**
     * Constructor.
     *
     * @param SchemaRegistryInterface $schemaRegistry
     * @param ObjectManager           $parameterManager
     * @param RepositoryInterface     $parameterRepository
     * @param Cache                   $cache
     */
    public function __construct(SchemaRegistryInterface $schemaRegistry, ObjectManager $parameterManager, RepositoryInterface $parameterRepository, Cache $cache)
    {
        $this->schemaRegistry = $schemaRegistry;
        $this->parameterManager = $parameterManager;
        $this->parameterRepository = $parameterRepository;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function loadSettings($namespace)
    {
        if (isset($this->resolvedSettings[$namespace])) {
            return $this->resolvedSettings[$namespace];
        }

        if ($this->cache->contains($namespace)) {
            $parameters = $this->cache->fetch($namespace);
        } else {
            $parameters = $this->getParameters($namespace);
        }

        $schema = $this->schemaRegistry->getSchema($namespace);

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);

        foreach ($settingsBuilder->getTransformers() as $parameter => $transformer) {
            if (array_key_exists($parameter, $parameters)) {
                $parameters[$parameter] = $transformer->reverseTransform($parameters[$parameter]);
            }
        }

        $parameters = $settingsBuilder->resolve($parameters);

        return $this->resolvedSettings[$namespace] = new Settings($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function saveSettings($namespace, Settings $settings)
    {
        $schema = $this->schemaRegistry->getSchema($namespace);

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);

        $parameters = $settingsBuilder->resolve($settings->getParameters());

        foreach ($settingsBuilder->getTransformers() as $parameter => $transformer) {
            if (array_key_exists($parameter, $parameters)) {
                $parameters[$parameter] = $transformer->transform($parameters[$parameter]);
            }
        }

        if (isset($this->resolvedSettings[$namespace])) {
            $this->resolvedSettings[$namespace]->setParameters($parameters);
        }

        $persistedParameters = $this->parameterRepository->findBy(array('namespace' => $namespace));
        $persistedParametersMap = array();

        foreach ($persistedParameters as $parameter) {
            $persistedParametersMap[$parameter->getName()] = $parameter;
        }

        foreach ($parameters as $name => $value) {
            if (isset($persistedParametersMap[$name])) {
                $persistedParametersMap[$name]->setValue($value);
            } else {
                $parameter = $this->parameterRepository->createNew();

                $parameter
                    ->setNamespace($namespace)
                    ->setName($name)
                    ->setValue($value)
                ;

                $this->parameterManager->persist($parameter);
            }
        }

        $this->parameterManager->flush();

        $this->cache->save($namespace, $parameters);
    }

    /**
     * Load parameter from database.
     *
     * @param string $namespace
     *
     * @return array
     */
    private function getParameters($namespace)
    {
        $parameters = array();

        foreach ($this->parameterRepository->findBy(array('namespace' => $namespace)) as $parameter) {
            $parameters[$parameter->getName()] = $parameter->getValue();
        }

        return $parameters;
    }
}
