<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\Factory;

use Payum\Core\Request\GetStatusInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;

final class GetStatusFactory implements GetStatusFactoryInterface
{
    public function createNewWithModel($model): GetStatusInterface
    {
        return new GetStatus($model);
    }
}
