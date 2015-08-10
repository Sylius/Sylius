<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Routing\Builder;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
interface RouteCollectionBuilderInterface
{
    /**
     * @param string      $application
     * @param string|null $prefix
     */
    public function createCollection($application, $prefix = null);

    /**
     * @param string $resource
     * @param string $action
     * @param array  $method
     */
    public function add($resource, $action, array $method);

    /**
     * @return RouteCollection
     */
    public function getCollection();
}
