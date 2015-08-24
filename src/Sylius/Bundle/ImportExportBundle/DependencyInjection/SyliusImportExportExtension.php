<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class SyliusImportExportExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_FORMS
        );

        $container->setParameter('sylius.import_export.timezone', $config['timezone']);
        $container->setParameter('sylius.import_export.log_path', $config['log_path']);

        $container
            ->getDefinition('sylius.form.type.export_profile')
            ->addArgument(new Reference('sylius.registry.export.reader'))
            ->addArgument(new Reference('sylius.registry.export.writer'))
        ;

        $container
            ->getDefinition('sylius.form.type.import_profile')
            ->addArgument(new Reference('sylius.registry.import.reader'))
            ->addArgument(new Reference('sylius.registry.import.writer'))
        ;
    }
}
