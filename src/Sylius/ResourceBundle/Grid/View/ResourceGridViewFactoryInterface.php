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

use Sylius\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Grid\Definition\Grid;
use Sylius\Grid\Parameters;
use Sylius\Resource\Metadata\MetadataInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ResourceGridViewFactoryInterface
{
    /**
     * @param Grid $grid
     * @param Parameters $parameters
     * @param MetadataInterface $metadata
     * @param RequestConfiguration $requestConfiguration
     *
     * @return ResourceGridView
     */
    public function create(Grid $grid, Parameters $parameters, MetadataInterface $metadata, RequestConfiguration $requestConfiguration);
}
