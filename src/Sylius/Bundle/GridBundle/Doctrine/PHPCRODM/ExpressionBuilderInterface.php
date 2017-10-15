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

namespace Sylius\Bundle\GridBundle\Doctrine\PHPCRODM;

use Sylius\Component\Grid\Data\ExpressionBuilderInterface as BaseExpressionBuilderInterface;

interface ExpressionBuilderInterface extends BaseExpressionBuilderInterface
{
    /**
     * @return array
     */
    public function getOrderBys(): array;
}
