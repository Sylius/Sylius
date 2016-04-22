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

use Doctrine\Common\Util\ClassUtils;
use Symfony\Cmf\Component\Routing\ContentAwareGenerator as BaseContentAwareGenerator;

class SyliusAwareGenerator extends BaseContentAwareGenerator
{
    /**
     * @var array
     */
    protected $routeConfig = [];

    /**
     * @param array $routeConfig
     */
    public function setRouteConfig(array $routeConfig)
    {
        $this->routeConfig = $routeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $absolute = false)
    {
        if ($this->isSyliusClassInstance($name)) {
            return parent::generate($this->getRouteByName($name, $parameters), $parameters, $absolute);
        }

        return parent::generate($name, $parameters, $absolute);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return parent::supports($name) || $this->isSyliusClassInstance($name);
    }

    /**
     * @param mixed $object
     *
     * @return bool
     */
    private function isSyliusClassInstance($object)
    {
        return is_object($object) && isset($this->routeConfig[ClassUtils::getClass($object)]);
    }
}
