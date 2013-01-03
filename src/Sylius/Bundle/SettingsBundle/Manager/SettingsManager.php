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

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface;

/**
 * Settings manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SettingsManager implements SettingsManagerInterface
{
    protected $schemaRegistry;
    protected $manager;
    protected $repository;
    protected $cache;

    public function __construct(SchemaRegistryInterface $schemaRegistry, Cache $cache, ObjectManager $manager, ObjectRepository $repository)
    {
        $this->schemaRegistry = $schemaRegistry;
        $this->manager = $manager;
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function loadSettings($namespace)
    {
        $schema = $this->schemaRegistry->getSchema($namespace);

        if ($this->cache->contains($namespace)) {
            $this->reverseTransform($schema, $this->cache->fetch($namespace));
        }

        $parameters = $this->getParameters($namespace);
        $settings = array();

        foreach ($parameters as $parameter) {
            $settings[$parameter->getName()] = $parameter->getValue();
        }

        $this->cache->save($namespace, $settings);

        return $this->reverseTransform($schema, $settings);
    }

    public function saveSettings($namespace, array $settings)
    {
        $schema = $this->schemaRegistry->getSchema($namespace);
        $settings = $this->transform($schema, $settings);

        $parameters = $this->getParameters($namespace);
        $originalSettings = $settings;

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();

            if (isset($settings[$name])) {
                $parameter->setValue($settings[$name]);

                $this->manager->persist($parameter);

                unset($settings[$name]);
            }
        }

        foreach ($settings as $name => $value) {
            $parameter = $this->repository->createNew();

            $parameter->setNamespace($namespace);
            $parameter->setName($name);
            $parameter->setValue($value);

            $this->manager->persist($parameter);
        }

        $this->cache->save($namespace, $originalSettings);

        $this->manager->flush();
    }

    private function transform(SchemaInterface $schema, array $settings)
    {
        $transformers = $schema->getDataTransformers();

        foreach ($transformers as $key => $transformer) {
            if (!$transformer instanceof DataTransformerInterface) {
                throw new \InvalidArgumentException('Settings parameter transformers must be instance of "Symfony\Component\Form\DataTransformerInterface"');
            }

            if (array_key_exists($key, $settings)) {
                $settings[$key] = $transformer->transform($settings[$key]);
            }
        }

        return $settings;
    }

    private function reverseTransform(SchemaInterface $schema, array $settings)
    {
        $transformers = $schema->getDataTransformers();

        foreach ($transformers as $key => $transformer) {
            if (!$transformer instanceof DataTransformerInterface) {
                throw new \InvalidArgumentException('Settings parameter transformers must be instance of "Symfony\Component\Form\DataTransformerInterface"');
            }

            if (array_key_exists($key, $settings)) {
                $settings[$key] = $transformer->reverseTransform($settings[$key]);
            }
        }

        return $settings;
    }

    private function getParameters($namespace)
    {
        return $this->repository->findBy(array('namespace' => $namespace));
    }
}
