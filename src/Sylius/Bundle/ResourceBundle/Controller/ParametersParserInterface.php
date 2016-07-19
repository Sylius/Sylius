<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ParametersParserInterface
{
    /**
     * @param array $parameters
     * @param Request $request
     *
     * @return array
     */
    public function parseRequestValues(array $parameters, Request $request);
}
