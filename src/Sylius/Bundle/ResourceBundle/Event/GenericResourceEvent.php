<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Event;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Resource event.
 *
 * @param Paweł Jędrzejewski <pawel@sylius.org>
 */
class GenericResourceEvent extends Event
{
    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * @var ResourceMetadataInterface
     */
    protected $metadata;

    /**
     * @var RequestConfiguration
     */
    protected $requestConfiguration;

    /**
     * @var string
     */
    protected $actionName;

    /**
     * @param ResourceInterface         $resource
     * @param ResourceMetadataInterface $metadata
     * @param RequestConfiguration      $requestConfiguration
     * @param string                    $actionName
     */
    public function __construct(
        ResourceInterface $resource,
        ResourceMetadataInterface $metadata,
        RequestConfiguration $requestConfiguration,
        $actionName
    )
    {
        $this->resource = $resource;
        $this->metadata = $metadata;
        $this->requestConfiguration = $requestConfiguration;
        $this->actionName = $actionName;
    }

    /**
     * @return ResourceInterface
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return ResourceMetadataInterface
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return RequestConfiguration
     */
    public function getRequestConfiguration()
    {
        return $this->requestConfiguration;
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }
}
