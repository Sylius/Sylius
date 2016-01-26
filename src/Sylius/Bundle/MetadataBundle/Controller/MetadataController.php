<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function customizeAction(Request $request)
    {
        try {
            return $this->updateAction($request);
        } catch (NotFoundHttpException $exception) {
            return $this->createAction($request);
        }
    }
}
