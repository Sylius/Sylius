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
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Bundle\SettingsBundle\Resolver\SettingsResolverInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsManager implements SettingsManagerInterface
{
    /**
     * @var SchemaRegistryInterface
     */
    protected $schemaRegistry;

    /**
     * @var ObjectManager
     */
    protected $settingsManager;

    /**
     * @var FactoryInterface
     */
    protected $settingsFactory;

    /**
     * @var SettingsResolverInterface
     */
    protected $defaultResolver;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * Runtime cache for resolved parameters.
     *
     * @var Settings[]
     */
    protected $resolvedSettings = [];

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(
        SchemaRegistryInterface $schemaRegistry,
        ObjectManager $settingsManager,
        FactoryInterface $settingsFactory,
        SettingsResolverInterface $defaultResolver,
        Cache $cache,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->schemaRegistry = $schemaRegistry;
        $this->settingsManager = $settingsManager;
        $this->settingsFactory = $settingsFactory;
        $this->defaultResolver = $defaultResolver;
        $this->cache = $cache;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function load($schemaAlias, array $context = [], $ignoreUnknown = true)
    {
        $schema = $this->schemaRegistry->getSchema($schemaAlias);

        // try to resolve schema settings
        $settings = $this->defaultResolver->resolve($schemaAlias);

        // if we could not resolve any existing settings, create a new one
        if (!$settings) {
            $settings = $this->settingsFactory->createNew();
            $settings->setSchema($schemaAlias);
        }

        $parameters = $settings->getParameters();

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);

        // Remove unknown settings' parameters (e.g. From a previous version of the settings schema)
        if (true === $ignoreUnknown) {
            foreach ($parameters as $name => $value) {
                if (!$settingsBuilder->isDefined($name)) {
                    unset($parameters[$name]);
                }
            }
        }

        $parameters = $this->transformParameters($settingsBuilder, $parameters);
        $parameters = $settingsBuilder->resolve($parameters);

        $settings->setParameters($parameters);

        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SettingsInterface $settings)
    {
        $this->settingsManager->persist($settings);
        $this->settingsManager->flush();
    }

    /**
     * @param SettingsBuilder $settingsBuilder
     * @param array           $parameters
     *
     * @return array
     */
    private function transformParameters(SettingsBuilder $settingsBuilder, array $parameters)
    {
        $transformedParameters = $parameters;

        foreach ($settingsBuilder->getTransformers() as $parameter => $transformer) {
            if (array_key_exists($parameter, $parameters)) {
                $transformedParameters[$parameter] = $transformer->reverseTransform($parameters[$parameter]);
            }
        }

        return $transformedParameters;
    }
}
