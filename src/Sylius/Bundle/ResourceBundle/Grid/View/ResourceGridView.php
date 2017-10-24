<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Grid\View;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Resource\Metadata\MetadataInterface;

class ResourceGridView extends GridView
{
    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @var RequestConfiguration
     */
    private $requestConfiguration;

    /**
     * @param mixed $data
     * @param Grid $gridDefinition
     * @param Parameters $parameters
     * @param MetadataInterface $resourceMetadata
     * @param RequestConfiguration $requestConfiguration
     */
    public function __construct(
        $data,
        Grid $gridDefinition,
        Parameters $parameters,
        MetadataInterface $resourceMetadata,
        RequestConfiguration $requestConfiguration
    ) {
        parent::__construct($data, $gridDefinition, $parameters);

        $this->metadata = $resourceMetadata;
        $this->requestConfiguration = $requestConfiguration;
    }

    /**
     * @return MetadataInterface
     */
    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    /**
     * @return RequestConfiguration
     */
    public function getRequestConfiguration(): RequestConfiguration
    {
        return $this->requestConfiguration;
    }
}
