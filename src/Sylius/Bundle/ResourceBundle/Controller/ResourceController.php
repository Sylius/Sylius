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

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\ResourceBundle\Event\GenericResourceEvent;
use Sylius\Bundle\ResourceBundle\Event\GenericResourceEvents;
use Sylius\Bundle\ResourceBundle\Form\Factory\ResourceFormFactoryInterface;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface;
use Sylius\Component\Resource\EventDispatcher\ResourceEventDispatcherInterface;
use Sylius\Component\Resource\Event\ResourceEvents;
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;
use Sylius\Component\Resource\Manager\ResourceManagerInterface;
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\DependencyInjection\ContainerAware;
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
class ResourceController extends ContainerAware
{
    /**
     * @var ResourceMetadataInterface
     */
    protected $metadata;

    /**
     * @var RequestConfigurationFactoryInterface
     */
    protected $configurationFactory;

    /**
     * @var ResourceManagerInterface
     */
    protected $manager;

    /**
     * @var ResourceRepositoryInterface
     */
    protected $repository;

    /**
     * @var ResourceFactoryInterface
     */
    protected $factory;

    /**
     * @var ResourceFactoryInterface
     */
    protected $eventDispatcher;

    /**
     * @var ResourceFormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var StateMachineFactoryInterface
     */
    protected $stateMachineFactory;

    /**
     * @var RedirectHandler
     */
    protected $redirectHandler;

    /**
     * @var ViewHandlerInterface
     */
    protected $viewHandler;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var ResourceResolver
     */
    protected $resourceResolver;

    /**
     * @param ResourceMetadataInterface            $metadata,
     * @param RequestConfigurationFactoryInterface $configurationFactory
     * @param ResourceManagerInterface             $manager,
     * @param ResourceRepositoryInterface          $repository,
     * @param ResourceFactoryInterface             $factory,
     * @param ResourceEventDispatcherInterface     $eventDispatcher,
     * @param ResourceFormFactoryInterface         $formFactory,
     * @param ViewHandlerInterface                 $viewHandlerInterface
     * @param RedirectHandler                      $redirectHandler
     * @param AuthorizationCheckerInterface        $authorizationChecker
     */
    public function __construct(
        ResourceMetadataInterface            $metadata,
        RequestConfigurationFactoryInterface $configurationFactory,
        ResourceManagerInterface             $manager,
        ResourceRepositoryInterface          $repository,
        ResourceFactoryInterface             $factory,
        ResourceEventDispatcherInterface     $eventDispatcher,
        ResourceFormFactoryInterface         $formFactory,
        StateMachineFactoryInterface         $stateMachineFactory,
        ViewHandlerInterface                 $viewHandler,
        RedirectHandler                      $redirectHandler,
        ParametersParser                     $parametersParser,
        AuthorizationCheckerInterface        $authorizationChecker = null
    ) {
        $this->metadata             = $metadata;
        $this->configurationFactory = $configurationFactory;
        $this->manager              = $manager;
        $this->repository           = $repository;
        $this->factory              = $factory;
        $this->eventDispatcher      = $eventDispatcher;
        $this->formFactory          = $formFactory;
        $this->stateMachineFactory  = $stateMachineFactory;
        $this->viewHandler          = $viewHandler;
        $this->redirectHandler      = $redirectHandler;
        $this->authorizationChecker = $authorizationChecker;
        $this->resourceResolver     = new ResourceResolver($repository, $factory);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::SHOW);

        $resource = $this->findOr404($configuration);

        $this->eventDispatcher->dispatch(GenericResourceEvents::SHOW, new GenericResourceEvent($resource, $this->metadata, $configuration, ResourceActions::SHOW));
        $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::SHOW, $resource);

        $view = View::create($resource)
            ->setTemplate($configuration->getTemplate(ResourceActions::SHOW.'.html'))
            ->setTemplateVar($this->metadata->getResourceName())
        ;

        if ($configuration->isHtmlRequest()) {
            $view->setData(array(
                'metadata'                         => $this->metadata,
                'resource'                         => $resource,
                $this->metadata->getResourceName() => $resource,
            ));
        }

        return $this->handleView($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::INDEX);

        $criteria = $configuration->getCriteria();
        $sorting = $configuration->getSorting();

        if ($configuration->isPaginated()) {
            $resources = $this->resourceResolver->getResource($configuration, 'createPaginator', array($criteria, $sorting));

            $resources->setCurrentPage($request->get('page', 1), true, true);
            $resources->setMaxPerPage($configuration->getPaginationMaxPerPage());

            if (!$configuration->isHtmlRequest()) {
                $resources = $this->getPagerfantaFactory()->createRepresentation(
                    $resources,
                    new Route(
                        $request->attributes->get('_route'),
                        array_merge($request->attributes->get('_route_params'), $request->query->all())
                    )
                );
            }
        } else {
            $resources = $this->resourceResolver->getResource($configuration, 'findBy', array($criteria, $sorting, $configuration->getLimit()));
        }

        $view = View::create($resources)
            ->setTemplate($configuration->getTemplate('index.html'))
            ->setTemplateVar($this->metadata->getPluralResourceName())
        ;

        if (!$configuration->isHtmlRequest()) {
            $view->setData(array(
                'metadata'                               => $this->metadata,
                'resources'                              => $resources,
                $this->metadata->getPluralResourceName() => $resources,
            ));
        }

        return $this->handleView($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::CREATE);

        $resource = $this->createNew($configuration);
        $form = $this->createForm($configuration, $resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $this->eventDispatcher->dispatch(GenericResourceEvents::PRE_CREATE, new GenericResourceEvent($resource, $this->metadata, $configuration, ResourceActions::CREATE));
            $event = $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::PRE_CREATE, $resource);

            if ($event->isPropagationStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }

            $this->manager->persist($resource);
            $this->manager->flush();

            $this->eventDispatcher->dispatch(GenericResourceEvents::POST_CREATE, new GenericResourceEvent($resource, $this->metadata, $configuration, ResourceActions::CREATE));
            $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::POST_CREATE, $resource);

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle(View::create($resource, 201));
            }

            return $this->redirectHandler->redirectToResource($configuration, $resource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->handleView($configuration, View::create($form, 400));
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('create.html'))
            ->setData(array(
                'metadata'                         => $this->metadata,
                'resource'                         => $resource,
                $this->metadata->getResourceName() => $resource,
                'form'                             => $form->createView()
            ))
        ;

        return $this->handleView($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request)
    {
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);

        $resource = $this->findOr404($configuration);
        $form     = $this->createForm($configuration, $resource);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $this->eventDispatcher->dispatch(GenericResourceEvents::PRE_UPDATE, new GenericResourceEvent($resource, $this->metadata, $configuration, ResourceActions::UPDATE));
            $event = $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::PRE_UPDATE, $resource);

            if ($event->isPropagationStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }

            $this->manager->persist($resource);
            $this->manager->flush();

            $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::POST_UPDATE, $resource);
            $this->eventDispatcher->dispatch(GenericResourceEvents::POST_UPDATE, new GenericResourceEvent($resource, $this->metadata, $configuration, ResourceActions::UPDATE));

            if (!$configuration->isHtmlRequest()) {
                return $this->handleView($configuration, View::create(null, 204));
            }

            return $this->redirectHandler->redirectToResource($configuration, $resource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->handleView($configuration, View::create($form, 400));
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('create.html'))
            ->setData(array(
                'metadata'                         => $this->metadata,
                'resource'                         => $resource,
                $this->metadata->getResourceName() => $resource,
                'form'                             => $form->createView()
            ))
        ;

        return $this->handleView($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::DELETE);

        $resource = $this->findOr404($configuration);

        $this->eventDispatcher->dispatch(GenericResourceEvents::PRE_DELETE, new GenericResourceEvent($resource, $this->metadata, $configuration, ResourceActions::DELETE));
        $event = $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::PRE_DELETE, $resource);

        if ($event->isPropagationStopped() && !$configuration->isHtmlRequest()) {
            throw new HttpException($event->getErrorCode(), $event->getMessage());
        }

        $this->manager->remove($resource);
        $this->manager->flush();

        $this->eventDispatcher->dispatch(GenericResourceEvents::POST_DELETE, new GenericResourceEvent($resource, $this->metadata, $configuration, ResourceActions::DELETE));
        $event = $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::POST_DELETE, $resource);

        if (!$configuration->isHtmlRequest()) {
            return $this->handleView($configuration, View::create(204));
        }

        return $this->redirectHandler->redirectToIndex($configuration);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function enableAction(Request $request)
    {
        return $this->toggle($request, true);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function disableAction(Request $request)
    {
        return $this->toggle($request, false);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function restoreAction(Request $request)
    {
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::RESTORE);

        $resource = $this->findOr404($configuration);

        $this->eventDispatcher->dispatch(GenericResourceEvents::PRE_RESTORE, new GenericResourceEvent($resource, $this->metadata, $configuration, ResourceActions::RESTORE));
        $event = $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::PRE_RESTORE, $resource);

        if ($event->isPropagationStopped() && $this->configuration->isApiRequest()) {
            throw new HttpException($event->getErrorCode(), $event->getMessage());
        }

        $this->manager->restore($resource);
        $this->manager->flush();

        $event = $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::POST_RESTORE, $resource);
        $this->eventDispatcher->dispatch(GenericResourceEvents::POST_RESTORE, new GenericResourceEvent($resource, $this->metadata, $configuration, ResourceActions::RESTORE));

        if ($this->configuration->isApiRequest()) {
            return $this->handleView($configuration, View::create(204));
        }

        return $this->redirectHandler->redirectTo($resource);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function revertAction(Request $request)
    {
        $this->isGrantedOr403(ResourceActions::REVERT);

        $resource = $this->findOr404($request);

        $event = $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::PRE_REVERT, $resource);

        if ($event->isPropagationStopped() && $this->configuration->isApiRequest()) {
            throw new HttpException($event->getErrorCode(), $event->getMessage());
        }

        // @todo: Implement reverting process with a separate service.

        if ($this->configuration->isApiRequest()) {
            return $this->handleView($configuration, View::create(204));
        }

        return $this->redirectHandler->redirectTo($resource);
    }

    /**
     * @param Request $request
     * @param string  $transition
     * @param string  $graph
     *
     * @return RedirectResponse|Response
     */
    public function transitionAction(Request $request, $transition, $graph)
    {
        $this->isGrantedOr403(ResourceActions::TRANSITION);

        $resource = $this->findOr404($request);
        $stateMachine = $this->stateMachineFactory->get($resource, $graph);

        if (!$stateMachine->can($transition)) {
            throw new NotFoundHttpException(sprintf(
                'The requested transition "%s" cannot be applied on the given "%s" with graph "%s".',
                $transition,
                $this->metadata->getName(),
                $graph
            ));
        }

        $event = $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::PRE_TRANSITION, $resource);

        if ($event->isPropagationStopped() && $this->configuration->isApiRequest()) {
            throw new HttpException($event->getErrorCode(), $event->getMessage());
        }

        $stateMachine->apply($transition);

        $this->manager->persist($resource);
        $this->manager->flush();

        $this->eventDispatcher->dispatchResourceEvent(ResourceEvents::POST_TRANSITION, $resource);

        return $this->redirectHandler->redirectTo($resource);
    }

    /**
     * @param RequestConfiguration $configuration
     * @param object|null $resource
     * @param array       $options
     *
     * @return FormInterface
     */
    private function createForm(RequestConfiguration $configuration, $resource = null)
    {
        $form = $this->formFactory->createForm($configuration, $this->metadata);
        $form->setData($resource);

        return $form;
    }

    /**
     * @param RequestConfiguration $configuration
     * @param array                $criteria
     *
     * @return object
     *
     * @throws NotFoundHttpException
     */
    protected function findOr404(RequestConfiguration $configuration, array $criteria = array())
    {
        $request = $configuration->getRequest();

        if ($request->attributes->has('slug') || $request->query->has('slug')) {
            $default = array('slug' => $request->get('slug'));
        } elseif ($request->attributes->has('id') || $request->query->has('id')) {
            $default = array('id' => $request->get('id'));
        } else {
            $default = array();
        }

        $criteria = array_merge($default, $criteria);

        if (!$resource = call_user_func_array(
            array($this->repository, $configuration->getRepositoryMethod('findOneBy')),
            array($configuration->getRepositoryArguments($criteria))
        )) {
            throw new NotFoundHttpException(
                sprintf(
                    'Requested resource "%s" does not exist with these criteria: %s.',
                    $this->metadata->getAlias(),
                    json_encode($configuration->getCriteria($criteria))
                )
            );
        }
        return $resource;
    }

    /**
     * @param RequestConfiguration $configuration
     * @param View $view
     *
     * @return Response
     */
    protected function handleView(RequestConfiguration $configuration, View $view)
    {
        $this->viewHandler->setExclusionStrategyGroups($configuration->getSerializationGroups());

        if ($version = $configuration->getSerializationVersion()) {
            $this->viewHandler->setExclusionStrategyVersion($version);
        }

        return $this->viewHandler->handle($view);
    }

    /**
     * @param RequestConfiguration $configuration
     * @param string $permission
     *
     * @return bool
     */
    protected function isGrantedOr403(RequestConfiguration $configuration, $permission)
    {
        if (null === $this->authorizationChecker) {
            return true;
        }

        $permission = $configuration->getPermission($permission);

        if (!$permission) {
            return true;
        }

        $permission = sprintf('%s.%s.%s', $this->metadata->getApplicationName(), $this->metadata->getResourceName(), $permission);

        if (!$this->authorizationChecker->isGranted($permission)) {
            throw new AccessDeniedException(sprintf('Access denied to "%s".', $permission));
        }

        return true;
    }

    /**
     * @param RequestConfiguration $configuration
     *
     * @return ResourceInterface
     */
    protected function createNew(RequestConfiguration $configuration)
    {
        return $this->resourceResolver->createResource($configuration);
    }

<<<<<<< HEAD
    protected function handleView(View $view)
    {
        $handler = $this->get('fos_rest.view_handler');
        $handler->setExclusionStrategyGroups($this->config->getSerializationGroups());

        if ($version = $this->config->getSerializationVersion()) {
            $handler->setExclusionStrategyVersion($version);
        }

        $view->getSerializationContext()->enableMaxDepthChecks();

        return $handler->handle($view);
    }

    protected function isGrantedOr403($permission)
=======
    /**
     * @return PagerfantaFactory
     */
    private function getPagerfantaFactory()
>>>>>>> Resource bundle refactoring
    {
        return new PagerfantaFactory();
    }
}
