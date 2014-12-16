<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Routing;

use Symfony\Cmf\Component\Routing\RouteProviderInterface as BaseRouteProviderInterface;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface RouteProviderInterface extends BaseRouteProviderInterface
{
    /**
     * This method is called from a compiler pass
     *
     * @param string           $class
     * @param ObjectRepository $repository
     */
    public function addRepository($class, ObjectRepository $repository);
} 