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
use FOS\RestBundle\View\View;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Sylius\Bundle\ResourceBundle\Form\DefaultFormFactory;
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

    /**
     * @var string
     */
    protected $stateMachineGraph;

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

            if (!$this->config->isApiRequest()) {
                $this->flashHelper = new FlashHelper(
                    $this->config,
                    $container->get('translator'),
                    $container->get('session')
                );
            }

            $this->domainManager = new DomainManager(
                $container->get($this->config->getServiceName('manager')),
                $container->get('event_dispatcher'),
                $this->config,
                !$this->config->isApiRequest() ? $this->flashHelper : null
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
        $this->isGrantedOr403('show');

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
        $this->isGrantedOr403('index');

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

            if ($this->config->isApiRequest()) {
                $resources = $this->getPagerfantaFactory()->createRepresentation(
                    $resources,
                    new Route(
                        $request->attributes->get('_route'),
                        array_merge($request->attributes->get('_route_params'), $request->query->all())
                    )
                );
            }
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
        $this->isGrantedOr403('create');

        $resource = $this->createNew();
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $resource = $this->domainManager->create($form->getData());

            if ($this->config->isApiRequest()) {
                if ($resource instanceof ResourceEvent) {
                    throw new HttpException($resource->getErrorCode(), $resource->getMessage());
                }

                return $this->handleView($this->view($resource, 201));
            }

            if ($resource instanceof ResourceEvent) {
                return $this->redirectHandler->redirectToIndex();
            }

            return $this->redirectHandler->redirectTo($resource);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView(),
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
        $this->isGrantedOr403('update');

        $resource = $this->findOr404($request);
        $form     = $this->getForm($resource);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $this->domainManager->update($resource);

            if ($this->config->isApiRequest()) {
                if ($resource instanceof ResourceEvent) {
                    throw new HttpException($resource->getErrorCode(), $resource->getMessage());
                }

                return $this->handleView($this->view($resource, 204));
            }

            if ($resource instanceof ResourceEvent) {
                return $this->redirectHandler->redirectToIndex();
            }

            return $this->redirectHandler->redirectTo($resource);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView(),
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $this->isGrantedOr403('delete');

        $resource = $this->domainManager->delete($this->findOr404($request));

        if ($this->config->isApiRequest()) {
            if ($resource instanceof ResourceEvent) {
                throw new HttpException($resource->getErrorCode(), $resource->getMessage());
            }

            return $this->handleView($this->view());
        }

        return $this->redirectHandler->redirectToIndex();
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function restoreAction(Request $request)
    {
        $this->get('doctrine')->getManager()->getFilters()->disable('softdeleteable');
        $resource = $this->findOr404($request);
        $resource->setDeletedAt(null);

        $this->domainManager->update($resource, 'restore_deleted');

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view());
        }

        return $this->redirectHandler->redirectTo($resource);
    }

    /**
     * @param Request $request
     * @param int     $version
     *
     * @return RedirectResponse
     */
    public function revertAction(Request $request, $version)
    {
        $resource   = $this->findOr404($request);
        $em         = $this->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('Gedmo\Loggable\Entity\LogEntry');
        $repository->revert($resource, $version);

        $this->domainManager->update($resource, 'revert');

        if ($this->config->isApiRequest()) {
            if ($resource instanceof ResourceEvent) {
                throw new HttpException($resource->getErrorCode(), $resource->getMessage());
            }

            return $this->handleView($this->view($resource, 204));
        }

        return $this->redirectHandler->redirectTo($resource);
    }

    public function moveUpAction(Request $request)
    {
        return $this->move($request, 1);
    }

    public function moveDownAction(Request $request)
    {
        return $this->move($request, -1);
    }

    public function updateStateAction(Request $request, $transition, $graph = null)
    {
        $resource = $this->findOr404($request);

        if (null === $graph) {
            $graph = $this->stateMachineGraph;
        }

        $stateMachine = $this->get('sm.factory')->get($resource, $graph);
        if (!$stateMachine->can($transition)) {
            throw new NotFoundHttpException(sprintf(
                'The requested transition %s cannot be applied on the given %s with graph %s.',
                $transition,
                $this->config->getResourceName(),
                $graph
            ));
        }

        $stateMachine->apply($transition);

        $this->domainManager->update($resource);

        if ($this->config->isApiRequest()) {
            if ($resource instanceof ResourceEvent) {
                throw new HttpException($resource->getErrorCode(), $resource->getMessage());
            }

            return $this->handleView($this->view($resource, 204));
        }

        return $this->redirectHandler->redirectToReferer();
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
        $type = $this->config->getFormType();

        if (strpos($type, '\\') !== false) { // full class name specified
            $type = new $type();
        } elseif (!$this->get('form.registry')->hasType($type)) { // form alias is not registered

            $defaultFormFactory = new DefaultFormFactory($this->container->get('form.factory'));

            return $defaultFormFactory->create($resource, $this->container->get($this->config->getServiceName('manager')));
        }

        if ($this->config->isApiRequest()) {
            return $this->container->get('form.factory')->createNamed('', $type, $resource, array('csrf_protection' => false));
        }

        return $this->createForm($type, $resource);
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

    /**
     * @param Request $request
     * @param integer $movement
     *
     * @return RedirectResponse
     */
    protected function move(Request $request, $movement)
    {
        $resource = $this->findOr404($request);

        $this->domainManager->move($resource, $movement);

        if ($this->config->isApiRequest()) {
            if ($resource instanceof ResourceEvent) {
                throw new HttpException($resource->getErrorCode(), $resource->getMessage());
            }

            return $this->handleView($this->view($resource, 204));
        }

        return $this->redirectHandler->redirectToIndex();
    }

    /**
     * @return PagerfantaFactory
     */
    protected function getPagerfantaFactory()
    {
        return new PagerfantaFactory();
    }

    protected function handleView(View $view)
    {
        $handler = $this->get('fos_rest.view_handler');
        $handler->setExclusionStrategyGroups($this->config->getSerializationGroups());

        if ($version = $this->config->getSerializationVersion()) {
            $handler->setExclusionStrategyVersion($version);
        }

        return $handler->handle($view);
    }

    protected function isGrantedOr403($permission)
    {
        if (!$this->container->has('sylius.authorization_checker')) {
            return true;
        }

        $permission = $this->config->getPermission($permission);

        if ($permission) {
            $grant = sprintf('%s.%s.%s', $this->config->getBundlePrefix(), $this->config->getResourceName(), $permission);

            if (!$this->get('sylius.authorization_checker')->isGranted($grant)) {
                throw new AccessDeniedException(sprintf('Access denied to "%s" for "%s".', $grant, $this->getUser() ? $this->getUser()->getUsername() : 'anon.'));
            }
        }
    }
}
