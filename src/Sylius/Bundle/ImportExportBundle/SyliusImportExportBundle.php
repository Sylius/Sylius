<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ImportExportBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ImportExportBundle\DependencyInjection\Compiler\RegisterWritersPass;
use Sylius\Bundle\ImportExportBundle\DependencyInjection\Compiler\RegisterReadersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Import/export component for Symfony2 applications.
 * It is used as a base for importing and exporting data.
 *
 * It is fully decoupled, so you can integrate it into your existing project.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class SyliusImportExportBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterWritersPass());
        $container->addCompilerPass(new RegisterReadersPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\ImportExport\Model\ExportJobInterface'     => 'sylius.model.export_job.class',
            'Sylius\Component\ImportExport\Model\ExportProfileInterface' => 'sylius.model.export_profile.class',
            'Sylius\Component\ImportExport\Model\ImportJobInterface'     => 'sylius.model.import_job.class',
            'Sylius\Component\ImportExport\Model\ImportProfileInterface' => 'sylius.model.import_profile.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\ImportExport\Model';
    }
}
