<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ResourceBundle\Grid\View;

use Sylius\ResourceBundle\Controller\ParametersParserInterface;
use Sylius\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Grid\Data\DataProviderInterface;
use Sylius\Grid\Definition\Grid;
use Sylius\Grid\Parameters;
use Sylius\Resource\Metadata\MetadataInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceGridViewFactory implements ResourceGridViewFactoryInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var ParametersParserInterface
     */
    private $parametersParser;

    /**
     * @param DataProviderInterface $dataProvider
     * @param ParametersParserInterface $parametersParser
     */
    public function __construct(DataProviderInterface $dataProvider, ParametersParserInterface $parametersParser)
    {
        $this->dataProvider = $dataProvider;
        $this->parametersParser = $parametersParser;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Grid $grid, Parameters $parameters, MetadataInterface $metadata, RequestConfiguration $requestConfiguration)
    {
        $driverConfiguration = $grid->getDriverConfiguration();
        $request = $requestConfiguration->getRequest();

        $grid->setDriverConfiguration($this->parametersParser->parseRequestValues($driverConfiguration, $request));

        return new ResourceGridView($this->dataProvider->getData($grid, $parameters), $grid, $parameters, $metadata, $requestConfiguration);
    }
}
