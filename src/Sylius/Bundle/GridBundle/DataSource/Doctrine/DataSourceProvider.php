<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\DataSource\Doctrine;

use Sylius\Bundle\GridBundle\DataSource\Doctrine\ORM\DataSource as DoctrineORMDataSource;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Grid\DataSource\DataSourceProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Doctrine datasource provider.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DataSourceProvider implements DataSourceProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSource(Grid $grid)
    {
        $repository = $this->container->get(sprintf('%s.repository.%s', $grid->getApplicationName(), $grid->getResourceName()));
        $parametersParser = $this->container->get('sylius.controller.parameters_parser');
        $request = $this->container->get('request');

        $options = $grid->getOptions();

        $method = null;
        $arguments = array();

        if (isset($options['repository']['method'])) {
            $method = $options['repository']['method'];

            if (isset($options['repository']['arguments'])) {
                $arguments = $parametersParser->parse($options['repository']['arguments'], $request);
                $arguments = $arguments[0];
            }
        }

        switch ($grid->getDriver()) {
            case SyliusResourceBundle::DRIVER_DOCTRINE_ORM:
                return new DoctrineORMDataSource($repository, $method, $arguments);
            break;

            default:
                throw new \InvalidArgumentException(sprintf('Driver "%s" is not supported by Sylius grids.', $grid->getDriver()));
            break;
        }
    }
}
