<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\Factory;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface DefaultResourceFormFactoryInterface
{
    /**
     * @param RequestConfiguration      $requestConfiguration
     * @param ResourceMetadataInterface $resourceMetadata
     *
     * @return FormInterface
     */
    public function create(RequestConfiguration $requestConfiguration, ResourceMetadataInterface $resourceMetadata);
}
