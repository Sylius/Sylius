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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base resource controller for Sylius.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ResourceController extends FOSRestController
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var FlashHelper
     */
    protected $flashHelper;

    /**
     * @var DomainManager
     */
    protected $domainManager;

    /**
     * @var ResourceResolver
     */
    protected $resourceResolver;

    /**
     * @var RedirectHandler
     */
    protected $redirectHandler;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    public function getConfiguration()
    {
        return $this->config;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $this->resourceResolver = new ResourceResolver($this->config);
        if (null !== $container) {
            $this->redirectHandler = new RedirectHandler($this->config, $container->get('router'));
            $this->flashHelper = new FlashHelper(
                $this->config,
                $container->get('translator'),
                $container->get('session')
            );
            $this->domainManager = new DomainManager(
                $container->get($this->config->getServiceName('manager')),
                $container->get('event_dispatcher'),
                $this->flashHelper,
                $this->config
            );
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('show.html'))
            ->setTemplateVar($this->config->getResourceName())
            ->setData($this->findOr404($request))
        ;

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $criteria = $this->config->getCriteria();
        $sorting = $this->config->getSorting();

        $repository = $this->getRepository();

        if ($this->config->isPaginated()) {
            $resources = $this->resourceResolver->getResource(
                $repository,
                'createPaginator',
                array($criteria, $sorting)
            );
            $resources->setCurrentPage($request->get('page', 1), true, true);
            $resources->setMaxPerPage($this->config->getPaginationMaxPerPage());
        } else {
            $resources = $this->resourceResolver->getResource(
                $repository,
                'findBy',
                array($criteria, $sorting, $this->config->getLimit())
            );
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData($resources)
        ;

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $resource = $this->createNew();
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $resource = $this->domainManager->create($resource);

            if (null === $resource) {
                return $this->redirectHandler->redirectToIndex();
            }

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

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request)
    {
        $resource = $this->findOr404($request);
        $form = $this->getForm($resource);

        if (($request->isMethod('PUT') || $request->isMethod('POST')) && $form->submit($request)->isValid()) {
            $this->domainManager->update($resource);

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

    public function moveUpAction(Request $request)
    {
        return $this->move($request, 1);
    }

    public function moveDownAction(Request $request)
    {
        return $this->move($request, -1);
    }

    /**
     * @param  Request          $request
     * @return RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $resource = $this->findOr404($request);
        $this->domainManager->delete($resource);

        return $this->redirectHandler->redirectToIndex();
    }

    /**
     * @return object
     */
    public function createNew()
    {
        return $this->resourceResolver->createResource($this->getRepository(), 'createNew');
    }

    /**
     * @param object|null $resource
     *
     * @return FormInterface
     */
    public function getForm($resource = null)
    {
        return $this->createForm($this->config->getFormType(), $resource);
    }

    /**
     * @param Request $request
     * @param array   $criteria
     *
     * @return object
     *
     * @throws NotFoundHttpException
     */
    public function findOr404(Request $request, array $criteria = array())
    {
        if ($request->get('slug')) {
            $default = array('slug' => $request->get('slug'));
        } elseif ($request->get('id')) {
            $default = array('id' => $request->get('id'));
        } else {
            $default = array();
        }

        $criteria = array_merge($default, $criteria);

        if (!$resource = $this->resourceResolver->getResource(
            $this->getRepository(),
            'findOneBy',
            array($this->config->getCriteria($criteria)))
        ) {
            throw new NotFoundHttpException(
                sprintf(
                    'Requested %s does not exist with these criteria: %s.',
                    $this->config->getResourceName(),
                    json_encode($this->config->getCriteria($criteria))
                )
            );
        }

        return $resource;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->get($this->config->getServiceName('repository'));
    }

    protected function move(Request $request, $movement)
    {
        $resource = $this->findOr404($request);

        $this->domainManager->move($resource, $movement);

        return $this->redirectHandler->redirectToIndex();
    }
}
