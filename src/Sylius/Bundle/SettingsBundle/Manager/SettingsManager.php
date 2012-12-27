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
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Settings manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SettingsManager implements SettingsManagerInterface
{
    protected $namespaces;
    protected $manager;
    protected $repository;
    protected $cache;

    public function __construct(array $namespaces, Cache $cache, ObjectManager $manager, ObjectRepository $repository)
    {
        $this->namespaces = $namespaces;
        $this->manager = $manager;
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function loadSettings($namespace)
    {
        if (!in_array($namespace, $this->namespaces)) {
            throw new \InvalidArgumentException(sprintf('Settings with namespace "%s" do not exist', $namespace));
        }

        if ($this->cache->contains($namespace)) {
            return $this->cache->fetch($namespace);
        }

        $parameters = $this->getParameters($namespace);
        $settings = array();

        foreach ($parameters as $parameter) {
            $settings[$parameter->getName()] = $parameter->getValue();
        }

        $this->cache->save($namespace, $settings);

        return $settings;
    }

    public function saveSettings($namespace, array $settings)
    {
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

    private function getParameters($namespace)
    {
        return $this->repository->findBy(array('namespace' => $namespace));
    }
}
