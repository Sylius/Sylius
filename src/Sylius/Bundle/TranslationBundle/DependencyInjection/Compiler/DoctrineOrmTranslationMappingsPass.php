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

/**
 * This compiler pass is used by AbstractResourceBundle to created the xml drivers needed for
 * resolving mapping translation file location
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class DoctrineOrmTranslationMappingsPass implements CompilerPassInterface
{

    /**
     * Driver definition
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
        $container->setDefinition(uniqid(), $this->driverDefinition);
    }

    /**
     * Creates the service definition for the translatable XML driver
     *
     * @param array $namespaces
     *
     * @return DoctrineOrmTranslationMappingsPass
     */
    public static function createXmlTranslationMappingDriver(array $namespaces)
    {
        $arguments = array($namespaces, '.orm.xml');

        $locator = new Definition('Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator', $arguments);

        //TODO inject xml driver?
        $driverDefinition = new Definition('Prezent\Doctrine\Translatable\Mapping\Driver\XmlDriver', array($locator));
        $driverDefinition->addTag('sylius.translation_driver');
        $driverDefinition->setPublic(false);

        return new DoctrineOrmTranslationMappingsPass($driverDefinition);
    }
} 