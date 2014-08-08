<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Component\Resource\Exception\Driver\UnknownDriverException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Abstract resource bundle.
 *
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
abstract class AbstractResourceBundle extends Bundle implements ResourceBundleInterface
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $interfaces = $this->getInterfaces($container);
        if (!empty($interfaces)) {
            $container->addCompilerPass(
                new ResolveDoctrineTargetEntitiesPass(
                    $this->getBundlePrefix(),
                    $interfaces
                )
            );
        }

        if (null !== $this->getEntityNamespace()) {
            $className = get_class($this);
            foreach ($className::getSupportedDrivers() as $driver) {
                list($mappingsPassClassName, $manager) = $this->getDoctrineDriver($driver);

                $container->addCompilerPass($mappingsPassClassName::createXmlMappingDriver(
                    array($this->getConfigFilesPath() => $this->getEntityNamespace()),
                    $manager,
                    sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                ));
            }
        }
    }

    /**
     * Return the prefix of the bundle
     *
     * @return string
     */
    abstract protected function getBundlePrefix();

    /**
     * Target entities resolver configuration (Interface - Model)
     *
     * @return array
     */
    protected function getInterfaces()
    {
        return array();
    }

    /**
     * Return the path to the Entity directory
     *
     * @return string
     */
    protected function getEntityDirectory()
    {
        return 'model';
    }

    /**
     * Return the entity namespace
     *
     * @return string
     */
    protected function getEntityNamespace()
    {
        return null;
    }

    /**
     * Return the entity manager
     *
     * @param string $driverType
     *
     * @return array
     *
     * @throws UnknownDriverException
     */
    protected function getDoctrineDriver($driverType)
    {
        switch ($driverType) {
            case SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM:
                return array(
                    'Doctrine\\Bundle\PHPCRBundle\\DependencyInjection\\Compiler\\DoctrinePhpcrMappingsPass',
                    array('doctrine_mongodb.odm.document_manager'),
                );
            case SyliusResourceBundle::DRIVER_DOCTRINE_ORM:
                return array(
                    'Doctrine\\Bundle\\DoctrineBundle\\DependencyInjection\\Compiler\\DoctrineOrmMappingsPass',
                    array('doctrine.orm.entity_manager'),
                );
            case SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM:
                return array(
                    'Doctrine\\Bundle\\MongoDBBundle\\DependencyInjection\\Compiler\\DoctrineMongoDBMappingsPass',
                    array('doctrine_phpcr.odm.document_manager'),
                );
        }

        throw new UnknownDriverException($driverType);
    }

    /**
     * Return the path to the xml directory
     *
     * @return string
     */
    protected function getConfigFilesPath()
    {
        return sprintf(
            '%s/Resources/config/doctrine/%s',
            $this->getPath(),
            strtolower($this->getEntityDirectory())
        );
    }
}
