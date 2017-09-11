<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Factory;

use Sylius\Bundle\PayumBundle\Request\ResolveNextRouteInterface;

interface ResolveNextRouteFactoryInterface
{
    public function createNewWithModel($model): ResolveNextRouteInterface;
}
