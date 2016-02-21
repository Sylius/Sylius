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
use Sylius\Bundle\SettingsBundle\Resolver\DefaultResolver;
use Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\ValidatorInterface;

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
     * @var RepositoryInterface
     */
    protected $settingsRepository;

    /**
     * @var FactoryInterface
     */
    protected $settingsFactory;

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
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param SchemaRegistryInterface  $schemaRegistry
     * @param ObjectManager            $settingsManager
     * @param RepositoryInterface      $settingsRepository
     * @param FactoryInterface         $settingsFactory
     * @param Cache                    $cache
     * @param ValidatorInterface       $validator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        SchemaRegistryInterface $schemaRegistry,
        ObjectManager $settingsManager,
        RepositoryInterface $settingsRepository,
        FactoryInterface $settingsFactory,
        Cache $cache,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->schemaRegistry = $schemaRegistry;
        $this->settingsManager = $settingsManager;
        $this->settingsRepository = $settingsRepository;
        $this->settingsFactory = $settingsFactory;
        $this->cache = $cache;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param       $schemaAlias
     * @param array $context
     * @param bool  $ignoreUnknown
     *
     * @return array
     */
    public function load($schemaAlias, array $context = [], $ignoreUnknown = true)
    {
        $schema = $this->schemaRegistry->getSchema($schemaAlias);

        $contextResolver = new OptionsResolver();
        $schema->configureContext($contextResolver);

        // resolve optional schema context
        $context = $contextResolver->resolve($context);

        // we have a schema (theme) and some context ([]), get the correct resolver now
        $resolver = new DefaultResolver($this->settingsRepository);
        $settings = $resolver->resolve($schemaAlias, $context);

        // create a new one
        if (!$settings) {
            /** @var SettingsInterface $settings */
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

    public function save(SettingsInterface $settings)
    {
        $schema = $this->schemaRegistry->getSchema($settings->getSchema());

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);

        $parameters = $settingsBuilder->resolve($settings->getParameters());

        foreach ($settingsBuilder->getTransformers() as $parameter => $transformer) {
            if (array_key_exists($parameter, $parameters)) {
                $parameters[$parameter] = $transformer->transform($parameters[$parameter]);
            }
        }

        $settings->setParameters($parameters);

        $this->settingsManager->persist($settings);
        $this->settingsManager->flush();
    }

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
