<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Factory;

use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRouteInterface;

final class ResolveNextRouteFactory implements ResolveNextRouteFactoryInterface
{
    public function createNewWithModel($model): ResolveNextRouteInterface
    {
        return new ResolveNextRoute($model);
    }
}
