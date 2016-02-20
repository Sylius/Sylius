<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Grid\View;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Resource\Metadata\MetadataInterface;

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
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Grid $grid, Parameters $parameters, MetadataInterface $metadata, RequestConfiguration $requestConfiguration)
    {
        return new ResourceGridView($this->dataProvider->getData($grid, $parameters), $grid, $parameters, $metadata, $requestConfiguration);
    }
}
