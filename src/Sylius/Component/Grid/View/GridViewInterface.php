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

namespace Sylius\Component\Grid\View;

use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface GridViewInterface
{
    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return Grid
     */
    public function getDefinition();

    /**
     * @return Parameters
     */
    public function getParameters();

    /**
     * @param string $fieldName
     *
     * @return string
     */
    public function getSortingOrder($fieldName);

    /**
     * @param string $fieldName
     *
     * @return bool
     */
    public function isSortedBy($fieldName);
}
