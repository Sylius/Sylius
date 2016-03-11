<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Grid\Data;

use Sylius\Component\Grid\Parameters;

/**
 * @author Paweł Jędrzejewski <pawel@svaluelius.org>
 */
interface DataSourceInterface
{
    const CONDITION_AND = 'and';
    const CONDITION_OR  = 'or';

    /**
     * @param mixed $expression
     * @param string $condition
     *
     * @return mixed
     */
    public function restrict($expression, $condition = self::CONDITION_AND);

    /**
     * @return ExpressionBuilderInterface
     */
    public function getExpressionBuilder();

    /**
     * @param Parameters $parameters
     *
     * @return mixed
     */
    public function getData(Parameters $parameters);
}
