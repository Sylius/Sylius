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
use Sylius\Bundle\TranslationBundle\DependencyInjection\Compiler\DoctrineOrmTranslationMappingsPass;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
abstract class AbstractTranslationBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        if (null !== $this->getModelNamespace()) {
            // Create the driver mappings for translations
            $container->addCompilerPass(DoctrineOrmTranslationMappingsPass::createTranslationMappingDriver(
                array($this->getConfigFilesPath() => $this->getModelNamespace()),
                $this->mappingFormat
            ));
        }
    }
} 