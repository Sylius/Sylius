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

namespace Sylius\Component\Grid\Data;

use Sylius\Component\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@svaluelius.org>
 */
interface DataSourceInterface
{
    public const CONDITION_AND = 'and';
    public const CONDITION_OR = 'or';

    /**
     * @param mixed $expression
     * @param string $condition
     */
    public function restrict($expression, string $condition = self::CONDITION_AND): void;

    /**
     * @return ExpressionBuilderInterface
     */
    public function getExpressionBuilder(): ExpressionBuilderInterface;

    /**
     * @param Parameters $parameters
     *
     * @return mixed
     */
    public function getData(Parameters $parameters);
}
