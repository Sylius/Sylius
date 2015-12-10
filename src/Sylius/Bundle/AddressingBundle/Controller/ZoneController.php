<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sylius\Bundle\AddressingBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->isGrantedOr403('index');

        $criteria = $this->config->getCriteria();
        $sorting = $this->config->getSorting();
        $repository = $this->getRepository();

        $zones = $this->resourceResolver->getResource(
            $repository,
            'createPaginator',
            array($criteria, $sorting)
        );

        $zones->setCurrentPage($request->get('page', 1), true, true);
        $zones->setMaxPerPage($this->config->getPaginationMaxPerPage());

        $form = $this->createFormBuilder()
            ->add('type', 'sylius_zone_type_choice')
            ->getForm();

        $pluralName = $this->config->getPluralResourceName();

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setTemplateVar($pluralName)
            ->setData(array(
                $pluralName => $zones,
                'form'      => $form->createView(),
            ))
        ;

        return $this->handleView($view);
    }
}
