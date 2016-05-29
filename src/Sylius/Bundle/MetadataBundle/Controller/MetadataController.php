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
        $resourceParams = $request->attributes->get('_sylius');

        /**
         * Support for more dynamic routing by metadata 'type'.
         * The default route can be used without specifying which child form type should be used
         * by the MetadataContainerType, as long as you name it according to the naming conventions below:
         *
         * sylius_backend_metadata_container_customize:
         *       path: /customize/{type}/{code}
         *       methods: [GET, POST, PUT]
         *       defaults:
         *           _controller: sylius.controller.metadata_container:customizeAction
         *           _sylius:
         *               form:
         *                   type: sylius_metadata_container
         *
         * Otherwise you can bypass the code below by explictly passing your metadata_form option, e.g.:
         *
         * sylius_backend_page_metadata_container_customize:
         *       path: /customize/page/{code}
         *       methods: [GET, POST, PUT]
         *       defaults:
         *           _controller: sylius.controller.metadata_container:customizeAction
         *           _sylius:
         *               form:
         *                   type: sylius_metadata_container
         *                   options:
         *                       metadata_form: sylius_page_metadata
         */
        if (!isset($resourceParams['form']['options']['metadata_form'])) {
            if ($type = $request->attributes->get('type')) {
                $resourceParams['form']['options']['metadata_form'] = sprintf('sylius_%s_metadata', $type);
                $request->attributes->set('_sylius', $resourceParams);
            } else {
                throw new \InvalidArgumentException('Please define the "metadata_form" param under _sylius: form: options: in your resource routing');
            }
        }

        try {
            return $this->updateAction($request);
        } catch (NotFoundHttpException $exception) {
            return $this->createAction($request);
        }
    }
}
