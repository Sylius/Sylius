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
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Abstract resource bundle.
 *
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
abstract class AbstractResourceBundle extends Bundle implements ResourceBundleInterface
{
    const MAPPING_XML = 'xml';
    const MAPPING_YAML = 'yml';

    /**
     * Configure format of mapping files.
     * @var string
     */
    protected $mappingFormat = self::MAPPING_XML;

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $interfaces = $this->getModelInterfaces();
        if (!empty($interfaces)) {
            $container->addCompilerPass(
                new ResolveDoctrineTargetEntitiesPass(
                    $this->getBundlePrefix(),
                    $interfaces
                )
            );
        }

        if (null !== $this->getModelNamespace()) {
            $className = get_class($this);
            foreach ($className::getSupportedDrivers() as $driver) {
                list($mappingsPassClassName, $manager) = $this->getMappingDriverInfo($driver);

                if (class_exists($mappingsPassClassName)) {
                    if (self::MAPPING_XML === $this->mappingFormat) {
                        $container->addCompilerPass($mappingsPassClassName::createXmlMappingDriver(
                            array($this->getConfigFilesPath() => $this->getModelNamespace()),
                            $manager,
                            sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                        ));
                    } elseif (self::MAPPING_YAML === $this->mappingFormat) {
                        $container->addCompilerPass($mappingsPassClassName::createYamlMappingDriver(
                            array($this->getConfigFilesPath() => $this->getModelNamespace()),
                            $manager,
                            sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                        ));
                    } else {
                        throw new InvalidConfigurationException("The 'mappingFormat' value is invalid, must be 'xml' or 'yml'.");
                    }
                }
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
    protected function getModelInterfaces()
    {
        return array();
    }

    /**
     * Return the directory where are stored the doctrine mapping
     *
     * @return string
     */
    protected function getDoctrineMappingDirectory()
    {
        return 'model';
    }

    /**
     * Return the entity namespace
     *
     * @return string
     */
    protected function getModelNamespace()
    {
        return null;
    }

    /**
     * Return informations used to initialize mapping driver
     *
     * @param string $driverType
     *
     * @return array
     *
     * @throws UnknownDriverException
     */
    protected function getMappingDriverInfo($driverType)
    {
        switch ($driverType) {
            case SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM:
                return array(
                    'Doctrine\\Bundle\\MongoDBBundle\\DependencyInjection\\Compiler\\DoctrineMongoDBMappingsPass',
                    array('doctrine_mongodb.odm.document_manager'),
                );
            case SyliusResourceBundle::DRIVER_DOCTRINE_ORM:
                return array(
                    'Doctrine\\Bundle\\DoctrineBundle\\DependencyInjection\\Compiler\\DoctrineOrmMappingsPass',
                    array('doctrine.orm.entity_manager'),
                );
            case SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM:
                return array(
                    'Doctrine\\Bundle\\PHPCRBundle\\DependencyInjection\\Compiler\\DoctrinePhpcrMappingsPass',
                    array('doctrine_phpcr.odm.document_manager'),
                );
        }

        throw new UnknownDriverException($driverType);
    }

    /**
     * Return the absolute path where are stored the doctrine mapping
     *
     * @return string
     */
    protected function getConfigFilesPath()
    {
        return sprintf(
            '%s/Resources/config/doctrine/%s',
            $this->getPath(),
            strtolower($this->getDoctrineMappingDirectory())
        );
    }
}
