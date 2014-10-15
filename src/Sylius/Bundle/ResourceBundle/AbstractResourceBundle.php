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
use Symfony\Component\DependencyInjection\Container;
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
    const MAPPING_ANNOTATION = 'annotation';

    /**
     * Configure format of mapping files.
     *
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
                $mappingsPassClassName = $this->getMappingDriverInfo($driver);

                if (class_exists($mappingsPassClassName)) {
                    switch ($this->mappingFormat){
                        case self::MAPPING_XML:
                            $container->addCompilerPass($mappingsPassClassName::createXmlMappingDriver(
                                array($this->getConfigFilesPath() => $this->getModelNamespace()),
                                array(sprintf('%s.object_manager', $this->getBundlePrefix())),
                                sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                            ));
                            break;
                        case self::MAPPING_YAML:
                            $container->addCompilerPass($mappingsPassClassName::createYamlMappingDriver(
                                array($this->getConfigFilesPath() => $this->getModelNamespace()),
                                array(sprintf('%s.object_manager', $this->getBundlePrefix())),
                                sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                            ));
                            break;
                        case self::MAPPING_ANNOTATION:
                            $container->addCompilerPass($mappingsPassClassName::createAnnotationMappingDriver(
                                array($this->getModelNamespace()),
                                array($this->getConfigFilesPath()),
                                array(sprintf('%s.object_manager', $this->getBundlePrefix())),
                                sprintf('%s.driver.%s', $this->getBundlePrefix(), $driver)
                            ));
                            break;
                        default:
                            throw new InvalidConfigurationException("The 'mappingFormat' value is invalid, must be 'xml', 'yml' or 'annotation'.");
                    }
                }
            }
        }
    }

    /**
     * Return the prefix of the bundle.
     *
     * @return string
     */
    protected function getBundlePrefix()
    {
        return Container::underscore(substr(strrchr(get_class($this), '\\'), 1, -6));
    }

    /**
     * Target entities resolver configuration (Interface - Model).
     *
     * @return array
     */
    protected function getModelInterfaces()
    {
        return array();
    }

    /**
     * Return the directory where are stored the doctrine mapping.
     *
     * @return string
     */
    protected function getDoctrineMappingDirectory()
    {
        return 'model';
    }

    /**
     * Return the entity namespace.
     *
     * @return string
     */
    protected function getModelNamespace()
    {
        return null;
    }

    /**
     * Return information's used to initialize mapping driver.
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
                return 'Doctrine\\Bundle\\MongoDBBundle\\DependencyInjection\\Compiler\\DoctrineMongoDBMappingsPass';
            case SyliusResourceBundle::DRIVER_DOCTRINE_ORM:
                return 'Doctrine\\Bundle\\DoctrineBundle\\DependencyInjection\\Compiler\\DoctrineOrmMappingsPass';
            case SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM:
                return 'Doctrine\\Bundle\\PHPCRBundle\\DependencyInjection\\Compiler\\DoctrinePhpcrMappingsPass';
        }

        throw new UnknownDriverException($driverType);
    }

    /**
     * Return the absolute path where are stored the doctrine mapping.
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
