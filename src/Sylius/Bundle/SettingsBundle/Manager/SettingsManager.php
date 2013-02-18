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
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
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
        if ($this->cache->contains($namespace)) {
            return $this->cache->fetch($namespace);
        }

        $schema = $this->schemaRegistry->getSchema($namespace);
        $parameters = $this->getParameters($namespace);

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);
        $parameters = $settingsBuilder->resolve($parameters);

        $settings = new Settings($parameters);

        return $settings;
    }

    public function saveSettings($namespace, SettingsInterface $settings)
    {
        $schema = $this->schemaRegistry->getSchema($namespace);

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);
        $parameters = $settingsBuilder->resolve($settings->getParameters());

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

        $settings->setParameters($parameters);

        $this->cache->save($namespace, $settings);
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

        foreach($this->parameterRepository->findBy(array('namespace' => $namespace)) as $parameter) {
            $parameters[$parameter->getName()] = $parameter->getValue();
        }

        return $parameters;
    }
}
