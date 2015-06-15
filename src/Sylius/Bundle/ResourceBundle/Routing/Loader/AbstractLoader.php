<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Routing\Loader;

use Sylius\Bundle\ResourceBundle\Routing\Builder\RouteCollectionBuilderInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
abstract class AbstractLoader implements LoaderInterface
{
    /**
     * @var RouteCollectionBuilderInterface
     */
    protected $collectionBuilder;

    /**
     * @var array
     */
    protected $resources;
    /**
     * @var array
     */
    protected $config;

    public function __construct(RouteCollectionBuilderInterface $collectionBuilder, array $resources, array $config)
    {
        $this->collectionBuilder = $collectionBuilder;
        $this->resources = $resources;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (1 < count($this->config)) {
            throw new \Exception('The router must be confiured');
        }

        foreach ($this->config as $application => $config) {
            $this->collectionBuilder->createCollection($application, $config['prefix']);

            if (isset($this->resources[$application])) {
                $syliusResources = array_keys($this->resources[$application]);
                foreach ($syliusResources as $syliusResource) {
                    $this->createResourceRoutes($syliusResource);
                }
            }
        }

        return $this->collectionBuilder->getCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return $this->getSupportedType() === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
        // Intentionally left blank
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        // Intentionally left blank
    }

    /**
     * @param string $resource
     */
    abstract protected function createResourceRoutes($resource);

    /**
     * @return string
     */
    abstract protected function getSupportedType();
}
