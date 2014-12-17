<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Sylius\Bundle\TranslationBundle\DependencyInjection\Compiler\DoctrineOrmTranslationMappingsPass;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
abstract class AbstractTranslationBundle extends AbstractResourceBundle{

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

//        if (null !== $this->getModelNamespace()) {
            //TODO create translation YML mapping driver!
//            if (self::MAPPING_XML === $this->mappingFormat) {
                // Create the xml driver services for translations
                $container->addCompilerPass(DoctrineOrmTranslationMappingsPass::createXmlTranslationMappingDriver(
                    array($this->getConfigFilesPath() => $this->getModelNamespace())
                ));

//            } else {
//                throw new InvalidConfigurationException("The translations 'mappingFormat' value is invalid, must be 'xml' .");
//            }
//        }
    }
} 