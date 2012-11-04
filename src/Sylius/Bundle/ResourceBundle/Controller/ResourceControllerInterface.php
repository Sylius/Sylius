<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Resource controlller interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ResourceControllerInterface
{
    function getAction(Request $request);
    function getCollectionAction(Request $request);
    function createAction(Request $request);
    function updateAction(Request $request);
    function deleteAction(Request $request);
}
