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
use Sylius\Bundle\SettingsBundle\Event\SettingsEvent;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
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
    protected $parameterManager;

    /**
     * @var RepositoryInterface
     */
    protected $parameterRepository;

    /**
     * @var FactoryInterface
     */
    protected $parameterFactory;

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
     * @param ObjectManager            $parameterManager
     * @param RepositoryInterface      $parameterRepository
     * @param FactoryInterface         $parameterFactory
     * @param Cache                    $cache
     * @param ValidatorInterface       $validator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        SchemaRegistryInterface $schemaRegistry,
        ObjectManager $parameterManager,
        RepositoryInterface $parameterRepository,
        FactoryInterface $parameterFactory,
        Cache $cache,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->schemaRegistry = $schemaRegistry;
        $this->parameterManager = $parameterManager;
        $this->parameterRepository = $parameterRepository;
        $this->parameterFactory = $parameterFactory;
        $this->cache = $cache;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function loadSettings($namespace, $ignoreUnknown = true)
    {
        if (isset($this->resolvedSettings[$namespace])) {
            return $this->resolvedSettings[$namespace];
        }

        if ($this->cache->contains($namespace)) {
            $parameters = $this->cache->fetch($namespace);
        } else {
            $parameters = $this->getParameters($namespace);
            $this->cache->save($namespace, $parameters);
        }

        $schema = $this->schemaRegistry->getSchema($namespace);

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

        return $this->resolvedSettings[$namespace] = new Settings($parameters);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ValidatorException
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
            $transformedParameters = $this->transformParameters($settingsBuilder, $parameters);
            $this->resolvedSettings[$namespace]->setParameters($transformedParameters);
        }

        $persistedParameters = $this->parameterRepository->findBy(['namespace' => $namespace]);
        $persistedParametersMap = [];

        foreach ($persistedParameters as $parameter) {
            $persistedParametersMap[$parameter->getName()] = $parameter;
        }

        $this->eventDispatcher->dispatch(SettingsEvent::PRE_SAVE, new SettingsEvent($namespace, $settings, $parameters));

        foreach ($parameters as $name => $value) {
            if (isset($persistedParametersMap[$name])) {
                $persistedParametersMap[$name]->setValue($value);
            } else {
                $parameter = $this->parameterFactory->createNew();

                $parameter->setNamespace($namespace);
                $parameter->setName($name);
                $parameter->setValue($value);

                /* @var $errors ConstraintViolationListInterface */
                $errors = $this->validator->validate($parameter);
                if (0 < $errors->count()) {
                    throw new ValidatorException($errors->get(0)->getMessage());
                }

                $this->parameterManager->persist($parameter);
            }
        }

        $this->parameterManager->flush();

        $this->eventDispatcher->dispatch(SettingsEvent::POST_SAVE, new SettingsEvent($namespace, $settings, $parameters));

        $this->cache->save($namespace, $parameters);
    }

    /**
     * @param string $namespace
     *
     * @return array
     */
    private function getParameters($namespace)
    {
        $parameters = [];

        foreach ($this->parameterRepository->findBy(['namespace' => $namespace]) as $parameter) {
            $parameters[$parameter->getName()] = $parameter->getValue();
        }

        return $parameters;
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
