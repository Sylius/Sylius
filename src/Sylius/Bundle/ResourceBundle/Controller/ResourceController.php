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

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base resource controller for Sylius.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ResourceController extends FOSRestController
{
    protected $config;
    protected $resourceResolver;
    protected $redirectHandler;
    protected $flashHelper;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $this->resourceResolver = new ResourceResolver($this->config);
        $this->redirectHandler = new RedirectHandler($this->config, $container->get('router'));
        $this->flashHelper = new FlashHelper($this->config, $container->get('translator'), $container->get('session'));
    }

    public function showAction()
    {
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('show.html'))
            ->setTemplateVar($this->config->getResourceName())
            ->setData($this->findOr404())
        ;

        return $this->handleView($view);
    }

    public function indexAction(Request $request)
    {
        $criteria = $this->config->getCriteria();
        $sorting = $this->config->getSorting();

        $pluralName = $this->config->getPluralResourceName();
        $repository = $this->getRepository();

        if ($this->config->isPaginated()) {
            $resources = $this->resourceResolver->getResource($repository, 'createPaginator', array($criteria, $sorting));

            $resources
                ->setCurrentPage($request->get('page', 1), true, true)
                ->setMaxPerPage($this->config->getPaginationMaxPerPage())
              ;
        } else {
            $resources = $this->resourceResolver->getResource($repository, 'findBy', array($criteria, $sorting, $this->config->getLimit()));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setTemplateVar($pluralName)
            ->setData($resources)
        ;

        return $this->handleView($view);
    }

    public function createAction(Request $request)
    {
        $resource = $this->createNew();
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->create($resource);

            return $this->redirectHandler->redirectTo($resource);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    public function updateAction(Request $request)
    {
        $resource = $this->findOr404();
        $form = $this->getForm($resource);

        if (($request->isMethod('PUT') || $request->isMethod('POST')) && $form->bind($request)->isValid()) {
            $this->update($resource);

            return $this->redirectHandler->redirectTo($resource);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    public function deleteAction()
    {
        $config = $this->getConfiguration();

        $resource = $this->findOr404();
        $this->delete($resource);

        return $this->redirectHandler->redirectToIndex($resource);
    }

    public function createNew()
    {
        return $this
            ->getRepository()
            ->createNew()
        ;
    }

    public function create($resource)
    {
        $this->dispatchEvent('pre_create', new GenericEvent($resource));
        $this->getManager()->persist($resource);
        $this->getManager()->flush();
        $this->flashHelper->setFlash('success', 'create');
        $this->dispatchEvent('post_create', new GenericEvent($resource));
    }

    public function update($resource)
    {
        $this->dispatchEvent('pre_update', new GenericEvent($resource));
        $this->getManager()->persist($resource);
        $this->getManager()->flush();
        $this->flashHelper->setFlash('success', 'update');
        $this->dispatchEvent('post_update', new GenericEvent($resource));
    }

    public function delete($resource)
    {
        $this->dispatchEvent('pre_delete', new GenericEvent($resource));
        $this->getManager()->remove($resource);
        $this->getManager()->flush();
        $this->flashHelper->setFlash('success', 'delete');
        $this->dispatchEvent('post_delete', new GenericEvent($resource));
    }

    public function getForm($resource = null)
    {
        return $this->createForm($this->config->getFormType(), $resource);
    }

    public function findOr404(array $criteria = null)
    {
        if (empty($criteria)) {
            $criteria = $this->config->getCriteria() ?: array('id' => $this->getRequest()->get('id'));
        }

        if (!$resource = $this->resourceResolver->getResource($this->getRepository(), 'findOneBy', array($criteria))) {
            throw new NotFoundHttpException('Requested resource does not exist.');
        }

        return $resource;
    }

    public function dispatchEvent($name, Event $event)
    {
        $name = $this->config->getEventName($name);

        return $this->get('event_dispatcher')->dispatch($name, $event);
    }

    public function getManager()
    {
        return $this->get($this->config->getServiceName('manager'));
    }

    public function getRepository()
    {
        return $this->get($this->config->getServiceName('repository'));
    }
}
