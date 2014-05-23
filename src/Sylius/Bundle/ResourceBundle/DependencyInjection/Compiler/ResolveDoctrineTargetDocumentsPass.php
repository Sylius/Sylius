<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ResourceBundle\DependencyInjection\DoctrineTargetDocumentsResolver;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Resolves given target documents with container parameters.
 * Usable only with *doctrine/orm* driver.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
class ResolveDoctrineTargetDocumentsPass implements CompilerPassInterface
{
    /**
     * @var array $interfaces
     */
    private $interfaces;

    /**
     * @var string $bundlePrefix
     */
    private $bundlePrefix;

    public function __construct($bundlePrefix, array $interfaces)
    {
        $this->bundlePrefix = $bundlePrefix;
        $this->interfaces = $interfaces;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM === $this->getDriver($container)) {
            $resolver = new DoctrineTargetDocumentsResolver();
            $resolver->resolve($container, $this->interfaces);
        }
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return string
     */
    private function getDriver(ContainerBuilder $container)
    {
        return $container->getParameter(sprintf('%s.driver', $this->bundlePrefix));
    }
}
