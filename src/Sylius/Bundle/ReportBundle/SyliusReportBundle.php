<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ReportBundle\DependencyInjection\Compiler\RegisterRenderersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sylius\Bundle\ReportBundle\DependencyInjection\Compiler\RegisterDataFetcherPass;

/**
 * Report component for Symfony2 applications.
 * It is used as a base for report management system inside Sylius.
 *
 * It is fully decoupled, so you can integrate it into your existing project.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusReportBundle extends AbstractResourceBundle
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

        $container->addCompilerPass(new RegisterDataFetcherPass());
        $container->addCompilerPass(new RegisterRenderersPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Report\Model\ReportInterface' => 'sylius.model.report.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Report\Model';
    }
}
