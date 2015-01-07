<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Sylius\Bundle\TranslationBundle\AbstractTranslationBundle;

/**
 * This compiler pass is used by AbstractResourceBundle to created the xml drivers needed for
 * resolving mapping translation file location
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class DoctrineOrmTranslationMappingsPass implements CompilerPassInterface
{

    /** definition
     *
     * @var \Symfony\Component\DependencyInjection\Definition
     */
    protected $driverDefinition;

    /**
     * @param Definition $driverDefinition
     */
    public function __construct(Definition $driverDefinition)
    {
        $this->driverDefinition = $driverDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $translatableDriverId = uniqid();
        $container->setDefinition($translatableDriverId, $this->driverDefinition);

        // Define service if not defined already
        if (!$container->hasDefinition('sylius.translatable.driver_chain')) {
            $driverChain = new Definition('Metadata\Driver\DriverChain');
            $driverChain->setPublic(false);
            $container->addDefinitions(array('sylius.translatable.driver_chain' => $driverChain));
        }

        // Add translatable driver to driver chain
        $container->getDefinition(
            'sylius.translatable.driver_chain'
        )->addMethodCall('addDriver', array(new Reference($translatableDriverId)));
    }

    /**
     * Creates the service definition for the translatable driver
     *
     * @param array  $namespaces
     * @param string $driver
     *
     * @throws InvalidConfigurationException
     * @return DoctrineOrmTranslationMappingsPass
     */
    public static function createTranslationMappingDriver(array $namespaces, $driver)
    {
        if (AbstractTranslationBundle::MAPPING_XML == $driver) {
            $arguments = array($namespaces, '.orm.xml');
            //TODO inject class?
            $class = 'Prezent\Doctrine\Translatable\Mapping\Driver\XmlDriver';
        } elseif (AbstractTranslationBundle::MAPPING_YAML == $driver) {
            $arguments = array($namespaces, '.orm.yml');
            //TODO inject class?
            $class = 'Prezent\Doctrine\Translatable\Mapping\Driver\YamlDriver';
        } else {
            throw new InvalidConfigurationException("The 'mappingFormat' value is invalid, must be 'xml' or 'yml'.");
        }

        $locator = new Definition('Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator', $arguments);

        $driverDefinition = new Definition($class, array($locator));
        $driverDefinition->setPublic(false);

        return new DoctrineOrmTranslationMappingsPass($driverDefinition);
    }
}