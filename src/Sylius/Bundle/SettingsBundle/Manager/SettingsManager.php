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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\SettingsBundle\Event\SettingsEvent;
use Sylius\Bundle\SettingsBundle\Model\ParameterCollection;
use Sylius\Bundle\SettingsBundle\Model\ParameterInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Bundle\SettingsBundle\Resolver\SettingsResolverInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilder;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class SettingsManager implements SettingsManagerInterface
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $schemaRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    protected $resolverRegistry;

    /**
     * @var ObjectManager
     */
    protected $settingsManager;

    /**
     * @var FactoryInterface
     */
    protected $settingsFactory;

    /**
     * @var FactoryInterface
     */
    protected $parameterFactory;

    /**
     * @var SettingsResolverInterface
     */
    protected $defaultResolver;

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

    public function __construct(
        ServiceRegistryInterface $schemaRegistry,
        ServiceRegistryInterface $resolverRegistry,
        ObjectManager $settingsManager,
        FactoryInterface $settingsFactory,
        FactoryInterface $parameterFactory,
        SettingsResolverInterface $defaultResolver,
        ValidatorInterface $validator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->schemaRegistry = $schemaRegistry;
        $this->resolverRegistry = $resolverRegistry;
        $this->settingsManager = $settingsManager;
        $this->settingsFactory = $settingsFactory;
        $this->parameterFactory = $parameterFactory;
        $this->defaultResolver = $defaultResolver;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function load($schemaAlias, $ignoreUnknown = true)
    {
        $schema = $this->schemaRegistry->get($schemaAlias);

        $resolver = $this->defaultResolver;

        if ($this->resolverRegistry->has($schemaAlias)) {
            $resolver = $this->resolverRegistry->get($schemaAlias);
        }

        $settings = $resolver->resolve($schemaAlias);

        if (!$settings) {
            $settings = $this->settingsFactory->createNew();
            $settings->setSchemaAlias($schemaAlias);
        }

        // map parameters to a plain php array
        $parameters = $settings->getParameters()
            ->map(function(ParameterInterface $parameter) {
                return $parameter->getValue();
            })->toArray()
        ;

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

        return new ParameterCollection($settings, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function save(ParameterCollection $parameters)
    {
        $settings = $parameters->getSettings();
        $parameters = $parameters->toArray();

        $schema = $this->schemaRegistry->get($settings->getSchemaAlias());

        $settingsBuilder = new SettingsBuilder();
        $schema->buildSettings($settingsBuilder);

        $parameters = $settingsBuilder->resolve($parameters);

        foreach ($settingsBuilder->getTransformers() as $parameter => $transformer) {
            if (array_key_exists($parameter, $parameters)) {
                $parameters[$parameter] = $transformer->transform($parameters[$parameter]);
            }
        }

        $this->eventDispatcher->dispatch(SettingsEvent::PRE_SAVE, new SettingsEvent($settings));

        foreach ($parameters as $name => $value) {
            if ($settings->hasParameter($name)) {
                $parameter = $settings->getParameter($name);
                $parameter->setValue($value);
            } else {
                /** @var ParameterInterface $parameter */
                $parameter = $this->parameterFactory->createNew();
                $parameter->setName($name);
                $parameter->setValue($value);

                /* @var $errors ConstraintViolationListInterface */
                $errors = $this->validator->validate($parameter);
                if (0 < $errors->count()) {
                    throw new ValidatorException($errors->get(0)->getMessage());
                }

                $settings->addParameter($parameter);
            }
        }

        $this->settingsManager->persist($settings);

        $this->settingsManager->flush();
        $this->eventDispatcher->dispatch(SettingsEvent::POST_SAVE, new SettingsEvent($settings));
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
