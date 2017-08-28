<?php

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Doctrine\PHPCRODM;

use Sylius\Component\Grid\Data\ExpressionBuilderInterface as BaseExpressionBuilderInterface;

interface ExpressionBuilderInterface extends BaseExpressionBuilderInterface
{
    /**
     * @return array
     */
    public function getOrderBys(): array;
}
