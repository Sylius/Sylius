<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Grid\Parser;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface OptionsParserInterface
{
    /**
     * @param array $parameters
     * @param Request $request
     * @param mixed $data
     *
     * @return array
     */
    public function parseOptions(array $parameters, Request $request, $data = null);
}
