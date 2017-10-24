<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\View\View;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\AuthorizationCheckerInterface;
use Sylius\Bundle\ResourceBundle\Controller\EventDispatcherInterface;
use Sylius\Bundle\ResourceBundle\Controller\FlashHelperInterface;
use Sylius\Bundle\ResourceBundle\Controller\NewResourceFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceDeleteHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceFormFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdateHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\SingleResourceProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\StateMachineInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Exception\DeleteHandlingException;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class ResourceControllerSpec extends ObjectBehavior
{
    function let(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        ViewHandlerInterface $viewHandler,
        RepositoryInterface $repository,
        FactoryInterface $factory,
        NewResourceFactoryInterface $newResourceFactory,
        ObjectManager $manager,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourcesCollectionProviderInterface $resourcesCollectionProvider,
        ResourceFormFactoryInterface $resourceFormFactory,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher,
        StateMachineInterface $stateMachine,
        ResourceUpdateHandlerInterface $resourceUpdateHandler,
        ResourceDeleteHandlerInterface $resourceDeleteHandler,
        ContainerInterface $container
    ): void {
        $this->beConstructedWith(
            $metadata,
            $requestConfigurationFactory,
            $viewHandler,
            $repository,
            $factory,
            $newResourceFactory,
            $manager,
            $singleResourceProvider,
            $resourcesCollectionProvider,
            $resourceFormFactory,
            $redirectHandler,
            $flashHelper,
            $authorizationChecker,
            $eventDispatcher,
            $stateMachine,
            $resourceUpdateHandler,
            $resourceDeleteHandler
        );

        $this->setContainer($container);
    }

    function it_extends_base_Symfony_controller(): void
    {
        $this->shouldHaveType(Controller::class);
    }

    function it_throws_a_403_exception_if_user_is_unauthorized_to_view_a_single_resource(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker
    ): void {
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::SHOW)->willReturn('sylius.product.show');

        $authorizationChecker->isGranted($configuration, 'sylius.product.show')->willReturn(false);

        $this
            ->shouldThrow(new AccessDeniedException())
            ->during('showAction', [$request])
        ;
    }

    function it_throws_a_404_exception_if_resource_is_not_found_based_on_configuration(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider
    ): void {
        $metadata->getHumanizedName()->willReturn('product');
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::SHOW)->willReturn('sylius.product.show');

        $authorizationChecker->isGranted($configuration, 'sylius.product.show')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn(null);

        $this
            ->shouldThrow(new NotFoundHttpException('The "product" has not been found'))
            ->during('showAction', [$request])
        ;
    }

    function it_returns_a_response_for_html_view_of_a_single_resource(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        ViewHandlerInterface $viewHandler,
        EventDispatcherInterface $eventDispatcher,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::SHOW)->willReturn('sylius.product.show');

        $authorizationChecker->isGranted($configuration, 'sylius.product.show')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::SHOW . '.html')->willReturn('SyliusShopBundle:Product:show.html.twig');

        $eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $resource)->shouldBeCalled();

        $expectedView = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $metadata,
                'resource' => $resource,
                'product' => $resource,
            ])
            ->setTemplateVar('product')
            ->setTemplate('SyliusShopBundle:Product:show.html.twig')
        ;

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->showAction($request)->shouldReturn($response);
    }

    function it_returns_a_response_for_non_html_view_of_single_resource(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        ViewHandlerInterface $viewHandler,
        EventDispatcherInterface $eventDispatcher,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::SHOW)->willReturn('sylius.product.show');

        $authorizationChecker->isGranted($configuration, 'sylius.product.show')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);

        $configuration->isHtmlRequest()->willReturn(false);

        $eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $resource)->shouldBeCalled();

        $expectedView = View::create($resource);

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->showAction($request)->shouldReturn($response);
    }

    function it_throws_a_403_exception_if_user_is_unauthorized_to_view_an_index_of_resources(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker
    ): void {
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::INDEX)->willReturn('sylius.product.index');

        $authorizationChecker->isGranted($configuration, 'sylius.product.index')->willReturn(false);

        $this
            ->shouldThrow(new AccessDeniedException())
            ->during('indexAction', [$request])
        ;
    }

    function it_returns_a_response_for_html_view_of_paginated_resources(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        ResourcesCollectionProviderInterface $resourcesCollectionProvider,
        EventDispatcherInterface $eventDispatcher,
        ResourceInterface $resource1,
        ResourceInterface $resource2,
        ViewHandlerInterface $viewHandler,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $metadata->getPluralName()->willReturn('products');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::INDEX)->willReturn('sylius.product.index');

        $authorizationChecker->isGranted($configuration, 'sylius.product.index')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::INDEX . '.html')->willReturn('SyliusShopBundle:Product:index.html.twig');
        $resourcesCollectionProvider->get($configuration, $repository)->willReturn([$resource1, $resource2]);

        $eventDispatcher->dispatchMultiple(ResourceActions::INDEX, $configuration, [$resource1, $resource2])->shouldBeCalled();

        $expectedView = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $metadata,
                'resources' => [$resource1, $resource2],
                'products' => [$resource1, $resource2],
            ])
            ->setTemplateVar('products')
            ->setTemplate('SyliusShopBundle:Product:index.html.twig')
        ;

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->indexAction($request)->shouldReturn($response);
    }

    function it_throws_a_403_exception_if_user_is_unauthorized_to_create_a_new_resource(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker
    ): void {
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::CREATE)->willReturn('sylius.product.create');

        $authorizationChecker->isGranted($configuration, 'sylius.product.create')->willReturn(false);

        $this
            ->shouldThrow(new AccessDeniedException())
            ->during('createAction', [$request])
        ;
    }

    function it_returns_a_html_response_for_creating_new_resource_form(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ViewHandlerInterface $viewHandler,
        FactoryInterface $factory,
        NewResourceFactoryInterface $newResourceFactory,
        ResourceInterface $newResource,
        ResourceFormFactoryInterface $resourceFormFactory,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        Form $form,
        FormView $formView,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::CREATE)->willReturn('sylius.product.create');

        $authorizationChecker->isGranted($configuration, 'sylius.product.create')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::CREATE . '.html')->willReturn('SyliusShopBundle:Product:create.html.twig');

        $newResourceFactory->create($configuration, $factory)->willReturn($newResource);
        $resourceFormFactory->create($configuration, $newResource)->willReturn($form);

        $eventDispatcher->dispatchInitializeEvent(ResourceActions::CREATE, $configuration, $newResource)->willReturn($event);

        $request->isMethod('POST')->willReturn(false);
        $form->createView()->willReturn($formView);

        $expectedView = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $metadata,
                'resource' => $newResource,
                'product' => $newResource,
                'form' => $formView,
            ])
            ->setTemplate('SyliusShopBundle:Product:create.html.twig')
        ;

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->createAction($request)->shouldReturn($response);
    }

    function it_returns_a_html_response_for_invalid_form_during_resource_creation(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ViewHandlerInterface $viewHandler,
        FactoryInterface $factory,
        NewResourceFactoryInterface $newResourceFactory,
        ResourceInterface $newResource,
        ResourceFormFactoryInterface $resourceFormFactory,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        Form $form,
        FormView $formView,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::CREATE)->willReturn('sylius.product.create');

        $authorizationChecker->isGranted($configuration, 'sylius.product.create')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::CREATE . '.html')->willReturn('SyliusShopBundle:Product:create.html.twig');

        $newResourceFactory->create($configuration, $factory)->willReturn($newResource);
        $resourceFormFactory->create($configuration, $newResource)->willReturn($form);

        $eventDispatcher->dispatchInitializeEvent(ResourceActions::CREATE, $configuration, $newResource)->willReturn($event);

        $request->isMethod('POST')->willReturn(true);
        $form->handleRequest($request)->willReturn($form);
        $form->isValid()->willReturn(false);
        $form->createView()->willReturn($formView);

        $expectedView = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $metadata,
                'resource' => $newResource,
                'product' => $newResource,
                'form' => $formView,
            ])
            ->setTemplate('SyliusShopBundle:Product:create.html.twig')
        ;

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->createAction($request)->shouldReturn($response);
    }

    function it_returns_a_non_html_response_for_invalid_form_during_resource_creation(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ViewHandlerInterface $viewHandler,
        FactoryInterface $factory,
        NewResourceFactoryInterface $newResourceFactory,
        ResourceInterface $newResource,
        ResourceFormFactoryInterface $resourceFormFactory,
        Form $form,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::CREATE)->willReturn('sylius.product.create');

        $authorizationChecker->isGranted($configuration, 'sylius.product.create')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(false);
        $configuration->getTemplate(ResourceActions::CREATE . '.html')->willReturn('SyliusShopBundle:Product:create.html.twig');

        $newResourceFactory->create($configuration, $factory)->willReturn($newResource);
        $resourceFormFactory->create($configuration, $newResource)->willReturn($form);

        $request->isMethod('POST')->willReturn(true);
        $form->handleRequest($request)->willReturn($form);
        $form->isValid()->willReturn(false);

        $expectedView = View::create($form, 400);

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->createAction($request)->shouldReturn($response);
    }

    function it_does_not_create_the_resource_and_redirects_to_index_for_html_requests_stopped_via_events(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ViewHandlerInterface $viewHandler,
        FactoryInterface $factory,
        NewResourceFactoryInterface $newResourceFactory,
        RepositoryInterface $repository,
        ResourceInterface $newResource,
        ResourceFormFactoryInterface $resourceFormFactory,
        Form $form,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::CREATE)->willReturn('sylius.product.create');

        $authorizationChecker->isGranted($configuration, 'sylius.product.create')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::CREATE . '.html')->willReturn('SyliusShopBundle:Product:create.html.twig');

        $newResourceFactory->create($configuration, $factory)->willReturn($newResource);
        $resourceFormFactory->create($configuration, $newResource)->willReturn($form);

        $request->isMethod('POST')->willReturn(true);
        $form->handleRequest($request)->willReturn($form);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($newResource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource)->willReturn($event);
        $event->isStopped()->willReturn(true);

        $flashHelper->addFlashFromEvent($configuration, $event)->shouldBeCalled();

        $event->hasResponse()->willReturn(false);

        $repository->add($newResource)->shouldNotBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource)->shouldNotBeCalled();
        $flashHelper->addSuccessFlash(Argument::any())->shouldNotBeCalled();

        $redirectHandler->redirectToIndex($configuration, $newResource)->willReturn($redirectResponse);

        $this->createAction($request)->shouldReturn($redirectResponse);
    }

    function it_does_not_create_the_resource_and_return_response_for_html_requests_stopped_via_events(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        FactoryInterface $factory,
        NewResourceFactoryInterface $newResourceFactory,
        RepositoryInterface $repository,
        ResourceInterface $newResource,
        ResourceFormFactoryInterface $resourceFormFactory,
        Form $form,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::CREATE)->willReturn('sylius.product.create');

        $authorizationChecker->isGranted($configuration, 'sylius.product.create')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::CREATE . '.html')->willReturn('SyliusShopBundle:Product:create.html.twig');

        $newResourceFactory->create($configuration, $factory)->willReturn($newResource);
        $resourceFormFactory->create($configuration, $newResource)->willReturn($form);

        $request->isMethod('POST')->willReturn(true);
        $form->handleRequest($request)->willReturn($form);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($newResource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource)->willReturn($event);
        $event->isStopped()->willReturn(true);

        $flashHelper->addFlashFromEvent($configuration, $event)->shouldBeCalled();

        $event->hasResponse()->willReturn(true);
        $event->getResponse()->willReturn($response);

        $repository->add($newResource)->shouldNotBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource)->shouldNotBeCalled();
        $flashHelper->addSuccessFlash(Argument::any())->shouldNotBeCalled();

        $this->createAction($request)->shouldReturn($response);
    }

    function it_redirects_to_newly_created_resource(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ViewHandlerInterface $viewHandler,
        FactoryInterface $factory,
        NewResourceFactoryInterface $newResourceFactory,
        RepositoryInterface $repository,
        ResourceInterface $newResource,
        ResourceFormFactoryInterface $resourceFormFactory,
        StateMachineInterface $stateMachine,
        Form $form,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        ResourceControllerEvent $postEvent,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::CREATE)->willReturn('sylius.product.create');
        $configuration->hasStateMachine()->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.create')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::CREATE . '.html')->willReturn('SyliusShopBundle:Product:create.html.twig');

        $newResourceFactory->create($configuration, $factory)->willReturn($newResource);
        $resourceFormFactory->create($configuration, $newResource)->willReturn($form);

        $request->isMethod('POST')->willReturn(true);
        $form->handleRequest($request)->willReturn($form);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($newResource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $stateMachine->apply($configuration, $newResource)->shouldBeCalled();

        $repository->add($newResource)->shouldBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource)->willReturn($postEvent);

        $postEvent->hasResponse()->willReturn(false);

        $flashHelper->addSuccessFlash($configuration, ResourceActions::CREATE, $newResource)->shouldBeCalled();
        $redirectHandler->redirectToResource($configuration, $newResource)->willReturn($redirectResponse);

        $this->createAction($request)->shouldReturn($redirectResponse);
    }

    function it_uses_response_from_post_create_event_if_defined(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        FactoryInterface $factory,
        NewResourceFactoryInterface $newResourceFactory,
        RepositoryInterface $repository,
        ResourceInterface $newResource,
        ResourceFormFactoryInterface $resourceFormFactory,
        StateMachineInterface $stateMachine,
        Form $form,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        ResourceControllerEvent $postEvent,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::CREATE)->willReturn('sylius.product.create');
        $configuration->hasStateMachine()->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.create')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::CREATE . '.html')->willReturn('SyliusShopBundle:Product:create.html.twig');

        $newResourceFactory->create($configuration, $factory)->willReturn($newResource);
        $resourceFormFactory->create($configuration, $newResource)->willReturn($form);

        $request->isMethod('POST')->willReturn(true);
        $form->handleRequest($request)->willReturn($form);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($newResource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $stateMachine->apply($configuration, $newResource)->shouldBeCalled();

        $repository->add($newResource)->shouldBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource)->willReturn($postEvent);
        $flashHelper->addSuccessFlash($configuration, ResourceActions::CREATE, $newResource)->shouldBeCalled();

        $postEvent->hasResponse()->willReturn(true);
        $postEvent->getResponse()->willReturn($redirectResponse);

        $this->createAction($request)->shouldReturn($redirectResponse);
    }

    function it_returns_a_non_html_response_for_correctly_created_resources(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ViewHandlerInterface $viewHandler,
        FactoryInterface $factory,
        NewResourceFactoryInterface $newResourceFactory,
        RepositoryInterface $repository,
        ResourceInterface $newResource,
        ResourceFormFactoryInterface $resourceFormFactory,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        StateMachineInterface $stateMachine,
        Form $form,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::CREATE)->willReturn('sylius.product.create');
        $configuration->hasStateMachine()->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.create')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(false);
        $configuration->getTemplate(ResourceActions::CREATE . '.html')->willReturn('SyliusShopBundle:Product:create.html.twig');

        $newResourceFactory->create($configuration, $factory)->willReturn($newResource);
        $resourceFormFactory->create($configuration, $newResource)->willReturn($form);

        $request->isMethod('POST')->willReturn(true);
        $form->handleRequest($request)->willReturn($form);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($newResource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $stateMachine->apply($configuration, $newResource)->shouldBeCalled();

        $repository->add($newResource)->shouldBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource)->shouldBeCalled();

        $flashHelper->addSuccessFlash(Argument::any())->shouldNotBeCalled();

        $expectedView = View::create($newResource, 201);

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->createAction($request)->shouldReturn($response);
    }

    function it_does_not_create_the_resource_and_throws_http_exception_for_non_html_requests_stopped_via_event(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        FactoryInterface $factory,
        NewResourceFactoryInterface $newResourceFactory,
        RepositoryInterface $repository,
        ResourceInterface $newResource,
        ResourceFormFactoryInterface $resourceFormFactory,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        Form $form,
        Request $request,
        ResourceControllerEvent $event
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::CREATE)->willReturn('sylius.product.create');

        $authorizationChecker->isGranted($configuration, 'sylius.product.create')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(false);
        $configuration->getTemplate(ResourceActions::CREATE . '.html')->willReturn('SyliusShopBundle:Product:create.html.twig');

        $newResourceFactory->create($configuration, $factory)->willReturn($newResource);
        $resourceFormFactory->create($configuration, $newResource)->willReturn($form);

        $request->isMethod('POST')->willReturn(true);
        $form->handleRequest($request)->willReturn($form);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($newResource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource)->willReturn($event);
        $event->isStopped()->willReturn(true);
        $event->getMessage()->willReturn('You cannot add a new product right now.');
        $event->getErrorCode()->willReturn(500);

        $repository->add($newResource)->shouldNotBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource)->shouldNotBeCalled();
        $flashHelper->addSuccessFlash(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new HttpException(500, 'You cannot add a new product right now.'))
            ->during('createAction', [$request])
        ;
    }

    function it_throws_a_403_exception_if_user_is_unauthorized_to_edit_a_single_resource(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker
    ): void {
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(false);

        $this
            ->shouldThrow(new AccessDeniedException())
            ->during('updateAction', [$request])
        ;
    }

    function it_throws_a_404_exception_if_resource_to_update_is_not_found_based_on_configuration(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider
    ): void {
        $metadata->getHumanizedName()->willReturn('product');
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn(null);

        $this
            ->shouldThrow(new NotFoundHttpException('The "product" has not been found'))
            ->during('updateAction', [$request])
        ;
    }

    function it_returns_a_html_response_for_updating_resource_form(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ViewHandlerInterface $viewHandler,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        ResourceFormFactoryInterface $resourceFormFactory,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        Form $form,
        FormView $formView,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');
        $configuration->hasStateMachine()->willReturn(false);

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::UPDATE . '.html')->willReturn('SyliusShopBundle:Product:update.html.twig');

        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resourceFormFactory->create($configuration, $resource)->willReturn($form);

        $eventDispatcher->dispatchInitializeEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);

        $request->isMethod('PATCH')->willReturn(false);
        $request->getMethod()->willReturn('GET');

        $form->handleRequest($request)->willReturn($form);
        $form->createView()->willReturn($formView);

        $expectedView = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $metadata,
                'resource' => $resource,
                'product' => $resource,
                'form' => $formView,
            ])
            ->setTemplate('SyliusShopBundle:Product:update.html.twig')
        ;

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->updateAction($request)->shouldReturn($response);
    }

    function it_returns_a_html_response_for_invalid_form_during_resource_update(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ViewHandlerInterface $viewHandler,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        ResourceFormFactoryInterface $resourceFormFactory,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        Form $form,
        FormView $formView,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::UPDATE . '.html')->willReturn('SyliusShopBundle:Product:update.html.twig');

        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resourceFormFactory->create($configuration, $resource)->willReturn($form);

        $eventDispatcher->dispatchInitializeEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);

        $request->isMethod('PATCH')->willReturn(false);
        $request->getMethod()->willReturn('PUT');

        $form->handleRequest($request)->willReturn($form);

        $form->isValid()->willReturn(false);
        $form->createView()->willReturn($formView);

        $expectedView = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $metadata,
                'resource' => $resource,
                'product' => $resource,
                'form' => $formView,
            ])
            ->setTemplate('SyliusShopBundle:Product:update.html.twig')
        ;

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->updateAction($request)->shouldReturn($response);
    }

    function it_returns_a_non_html_response_for_invalid_form_during_resource_update(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ViewHandlerInterface $viewHandler,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        ResourceFormFactoryInterface $resourceFormFactory,
        Form $form,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');
        $configuration->isHtmlRequest()->willReturn(false);

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);

        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resourceFormFactory->create($configuration, $resource)->willReturn($form);

        $request->isMethod('PATCH')->willReturn(true);
        $request->getMethod()->willReturn('PATCH');

        $form->handleRequest($request)->willReturn($form);
        $form->isValid()->willReturn(false);

        $expectedView = View::create($form, 400);
        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->updateAction($request)->shouldReturn($response);
    }

    function it_does_not_update_the_resource_and_redirects_to_resource_for_html_request_if_stopped_via_event(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ObjectManager $manager,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        ResourceFormFactoryInterface $resourceFormFactory,
        Form $form,
        EventDispatcherInterface $eventDispatcher,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        ResourceControllerEvent $event,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);

        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resourceFormFactory->create($configuration, $resource)->willReturn($form);

        $request->isMethod('PATCH')->willReturn(false);
        $request->getMethod()->willReturn('PUT');

        $form->handleRequest($request)->willReturn($form);

        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($resource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(true);
        $event->hasResponse()->willReturn(false);
        $flashHelper->addFlashFromEvent($configuration, $event)->shouldBeCalled();

        $manager->flush()->shouldNotBeCalled();
        $eventDispatcher->dispatchPostEvent(Argument::any())->shouldNotBeCalled();
        $flashHelper->addSuccessFlash(Argument::any())->shouldNotBeCalled();

        $redirectHandler->redirectToResource($configuration, $resource)->willReturn($redirectResponse);

        $this->updateAction($request)->shouldReturn($redirectResponse);
    }

    function it_redirects_to_updated_resource(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RepositoryInterface $repository,
        ObjectManager $manager,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceFormFactoryInterface $resourceFormFactory,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher,
        ResourceUpdateHandlerInterface $resourceUpdateHandler,
        RequestConfiguration $configuration,
        ResourceInterface $resource,
        Form $form,
        ResourceControllerEvent $preEvent,
        ResourceControllerEvent $postEvent,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');
        $configuration->hasStateMachine()->willReturn(false);

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::UPDATE . '.html')->willReturn('SyliusShopBundle:Product:update.html.twig');

        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resourceFormFactory->create($configuration, $resource)->willReturn($form);

        $request->isMethod('PATCH')->willReturn(false);
        $request->getMethod()->willReturn('PUT');

        $form->handleRequest($request)->willReturn($form);

        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($resource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($preEvent);
        $preEvent->isStopped()->willReturn(false);

        $resourceUpdateHandler->handle($resource, $configuration, $manager)->shouldBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($postEvent);

        $postEvent->hasResponse()->willReturn(false);

        $flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource)->shouldBeCalled();
        $redirectHandler->redirectToResource($configuration, $resource)->willReturn($redirectResponse);

        $this->updateAction($request)->shouldReturn($redirectResponse);
    }

    function it_uses_response_from_post_update_event_if_defined(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RepositoryInterface $repository,
        ObjectManager $manager,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceFormFactoryInterface $resourceFormFactory,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher,
        ResourceUpdateHandlerInterface $resourceUpdateHandler,
        RequestConfiguration $configuration,
        ResourceInterface $resource,
        Form $form,
        ResourceControllerEvent $preEvent,
        ResourceControllerEvent $postEvent,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');
        $configuration->hasStateMachine()->willReturn(false);

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::UPDATE . '.html')->willReturn('SyliusShopBundle:Product:update.html.twig');

        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resourceFormFactory->create($configuration, $resource)->willReturn($form);

        $request->isMethod('PATCH')->willReturn(false);
        $request->getMethod()->willReturn('PUT');

        $form->handleRequest($request)->willReturn($form);

        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($resource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($preEvent);
        $preEvent->isStopped()->willReturn(false);

        $resourceUpdateHandler->handle($resource, $configuration, $manager)->shouldBeCalled();
        $flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource)->shouldBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($postEvent);

        $postEvent->hasResponse()->willReturn(true);
        $postEvent->getResponse()->willReturn($redirectResponse);

        $redirectHandler->redirectToResource($configuration, $resource)->shouldNotBeCalled();

        $this->updateAction($request)->shouldReturn($redirectResponse);
    }

    function it_returns_a_non_html_response_for_correctly_updated_resource(
        MetadataInterface $metadata,
        ParameterBagInterface $parameterBag,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        ViewHandlerInterface $viewHandler,
        RepositoryInterface $repository,
        ObjectManager $manager,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceFormFactoryInterface $resourceFormFactory,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher,
        ResourceUpdateHandlerInterface $resourceUpdateHandler,
        RequestConfiguration $configuration,
        ResourceInterface $resource,
        ResourceControllerEvent $event,
        Form $form,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');
        $configuration->isHtmlRequest()->willReturn(false);
        $configuration->hasStateMachine()->willReturn(false);

        $configuration->getParameters()->willReturn($parameterBag);
        $parameterBag->get('return_content', false)->willReturn(false);

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);

        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resourceFormFactory->create($configuration, $resource)->willReturn($form);

        $request->isMethod('PATCH')->willReturn(false);
        $request->getMethod()->willReturn('PUT');

        $form->handleRequest($request)->willReturn($form);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($resource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $resourceUpdateHandler->handle($resource, $configuration, $manager)->shouldBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource)->shouldBeCalled();

        $expectedView = View::create(null, 204);
        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->updateAction($request)->shouldReturn($response);
    }

    function it_does_not_update_the_resource_throws_a_http_exception_for_non_html_requests_stopped_via_event(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ObjectManager $manager,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        ResourceFormFactoryInterface $resourceFormFactory,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        Form $form,
        Request $request
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');
        $configuration->isHtmlRequest()->willReturn(false);

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);

        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resourceFormFactory->create($configuration, $resource)->willReturn($form);

        $request->isMethod('PATCH')->willReturn(false);
        $request->getMethod()->willReturn('PUT');

        $form->handleRequest($request)->willReturn($form);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($resource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(true);
        $event->getMessage()->willReturn('Cannot update this channel.');
        $event->getErrorCode()->willReturn(500);

        $manager->flush()->shouldNotBeCalled();
        $eventDispatcher->dispatchPostEvent(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new HttpException(500, 'Cannot update this channel.'))
            ->during('updateAction', [$request])
        ;
    }

    function it_applies_state_machine_transition_to_updated_resource_if_configured(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RepositoryInterface $repository,
        ObjectManager $manager,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceFormFactoryInterface $resourceFormFactory,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher,
        ResourceUpdateHandlerInterface $resourceUpdateHandler,
        RequestConfiguration $configuration,
        ResourceInterface $resource,
        Form $form,
        ResourceControllerEvent $preEvent,
        ResourceControllerEvent $postEvent,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');
        $configuration->hasStateMachine()->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->getTemplate(ResourceActions::UPDATE)->willReturn('SyliusShopBundle:Product:update.html.twig');

        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resourceFormFactory->create($configuration, $resource)->willReturn($form);

        $request->isMethod('PATCH')->willReturn(false);
        $request->getMethod()->willReturn('PUT');

        $form->handleRequest($request)->willReturn($form);

        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);
        $form->getData()->willReturn($resource);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($preEvent);
        $preEvent->isStopped()->willReturn(false);

        $resourceUpdateHandler->handle($resource, $configuration, $manager)->shouldBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($postEvent);

        $postEvent->hasResponse()->willReturn(false);

        $flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource)->shouldBeCalled();
        $redirectHandler->redirectToResource($configuration, $resource)->willReturn($redirectResponse);

        $this->updateAction($request)->shouldReturn($redirectResponse);
    }

    function it_throws_a_403_exception_if_user_is_unauthorized_to_delete_a_single_resource(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker
    ): void {
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::DELETE)->willReturn('sylius.product.delete');

        $authorizationChecker->isGranted($configuration, 'sylius.product.delete')->willReturn(false);

        $this
            ->shouldThrow(new AccessDeniedException())
            ->during('deleteAction', [$request])
        ;
    }

    function it_throws_a_404_exception_if_resource_for_deletion_is_not_found_based_on_configuration(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider
    ): void {
        $metadata->getHumanizedName()->willReturn('product');
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::DELETE)->willReturn('sylius.product.delete');

        $authorizationChecker->isGranted($configuration, 'sylius.product.delete')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn(null);

        $this
            ->shouldThrow(new NotFoundHttpException('The "product" has not been found'))
            ->during('deleteAction', [$request])
        ;
    }

    function it_deletes_a_resource_and_redirects_to_index_by_for_html_request(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        CsrfTokenManagerInterface $csrfTokenManager,
        ContainerInterface $container,
        ResourceControllerEvent $event,
        ResourceControllerEvent $postEvent,
        ResourceDeleteHandlerInterface $resourceDeleteHandler,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::DELETE)->willReturn('sylius.product.delete');
        $request->request = new ParameterBag(['_csrf_token' => 'xyz']);

        $container->has('security.csrf.token_manager')->willReturn(true);
        $container->get('security.csrf.token_manager')->willReturn($csrfTokenManager);
        $csrfTokenManager->isTokenValid(new CsrfToken(1, 'xyz'))->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.delete')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resource->getId()->willReturn(1);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->isCsrfProtectionEnabled()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::DELETE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $resourceDeleteHandler->handle($resource, $repository)->shouldBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::DELETE, $configuration, $resource)->willReturn($postEvent);

        $postEvent->hasResponse()->willReturn(false);

        $flashHelper->addSuccessFlash($configuration, ResourceActions::DELETE, $resource)->shouldBeCalled();
        $redirectHandler->redirectToIndex($configuration, $resource)->willReturn($redirectResponse);

        $this->deleteAction($request)->shouldReturn($redirectResponse);
    }

    function it_uses_response_from_post_delete_event_if_defined(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        CsrfTokenManagerInterface $csrfTokenManager,
        ContainerInterface $container,
        ResourceControllerEvent $event,
        ResourceControllerEvent $postEvent,
        ResourceDeleteHandlerInterface $resourceDeleteHandler,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::DELETE)->willReturn('sylius.product.delete');
        $request->request = new ParameterBag(['_csrf_token' => 'xyz']);

        $container->has('security.csrf.token_manager')->willReturn(true);
        $container->get('security.csrf.token_manager')->willReturn($csrfTokenManager);
        $csrfTokenManager->isTokenValid(new CsrfToken(1, 'xyz'))->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.delete')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resource->getId()->willReturn(1);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->isCsrfProtectionEnabled()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::DELETE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $resourceDeleteHandler->handle($resource, $repository)->shouldBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::DELETE, $configuration, $resource)->willReturn($postEvent);

        $flashHelper->addSuccessFlash($configuration, ResourceActions::DELETE, $resource)->shouldBeCalled();

        $postEvent->hasResponse()->willReturn(true);
        $postEvent->getResponse()->willReturn($redirectResponse);

        $this->deleteAction($request)->shouldReturn($redirectResponse);
    }

    function it_does_not_delete_a_resource_and_redirects_to_index_for_html_requests_stopped_via_event(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        CsrfTokenManagerInterface $csrfTokenManager,
        ContainerInterface $container,
        ResourceControllerEvent $event,
        ResourceDeleteHandlerInterface $resourceDeleteHandler,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::DELETE)->willReturn('sylius.product.delete');
        $request->request = new ParameterBag(['_csrf_token' => 'xyz']);

        $container->has('security.csrf.token_manager')->willReturn(true);
        $container->get('security.csrf.token_manager')->willReturn($csrfTokenManager);
        $csrfTokenManager->isTokenValid(new CsrfToken(1, 'xyz'))->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.delete')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resource->getId()->willReturn(1);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->isCsrfProtectionEnabled()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::DELETE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(true);
        $event->hasResponse()->willReturn(false);

        $resourceDeleteHandler->handle($resource, $repository)->shouldNotBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::DELETE, $configuration, $resource)->shouldNotBeCalled();
        $flashHelper->addSuccessFlash($configuration, ResourceActions::DELETE, $resource)->shouldNotBeCalled();

        $flashHelper->addFlashFromEvent($configuration, $event)->shouldBeCalled();
        $redirectHandler->redirectToIndex($configuration, $resource)->willReturn($redirectResponse);

        $this->deleteAction($request)->shouldReturn($redirectResponse);
    }

    function it_does_not_delete_a_resource_and_uses_response_from_event_if_defined(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        CsrfTokenManagerInterface $csrfTokenManager,
        ContainerInterface $container,
        ResourceControllerEvent $event,
        ResourceDeleteHandlerInterface $resourceDeleteHandler,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::DELETE)->willReturn('sylius.product.delete');
        $request->request = new ParameterBag(['_csrf_token' => 'xyz']);

        $container->has('security.csrf.token_manager')->willReturn(true);
        $container->get('security.csrf.token_manager')->willReturn($csrfTokenManager);
        $csrfTokenManager->isTokenValid(new CsrfToken(1, 'xyz'))->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.delete')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resource->getId()->willReturn(1);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->isCsrfProtectionEnabled()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::DELETE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(true);

        $flashHelper->addFlashFromEvent($configuration, $event)->shouldBeCalled();

        $event->hasResponse()->willReturn(true);
        $event->getResponse()->willReturn($redirectResponse);

        $resourceDeleteHandler->handle($resource, $repository)->shouldNotBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::DELETE, $configuration, $resource)->shouldNotBeCalled();
        $flashHelper->addSuccessFlash($configuration, ResourceActions::DELETE, $resource)->shouldNotBeCalled();

        $redirectHandler->redirectToIndex($configuration, $resource)->shouldNotBeCalled();

        $this->deleteAction($request)->shouldReturn($redirectResponse);
    }

    function it_does_not_correctly_delete_a_resource_and_returns_500_for_not_html_response(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ViewHandlerInterface $viewHandler,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        EventDispatcherInterface $eventDispatcher,
        CsrfTokenManagerInterface $csrfTokenManager,
        ContainerInterface $container,
        ResourceControllerEvent $event,
        ResourceDeleteHandlerInterface $resourceDeleteHandler,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::DELETE)->willReturn('sylius.product.delete');
        $request->request = new ParameterBag(['_csrf_token' => 'xyz']);

        $container->has('security.csrf.token_manager')->willReturn(true);
        $container->get('security.csrf.token_manager')->willReturn($csrfTokenManager);
        $csrfTokenManager->isTokenValid(new CsrfToken(1, 'xyz'))->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.delete')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resource->getId()->willReturn(1);

        $configuration->isHtmlRequest()->willReturn(false);
        $configuration->isCsrfProtectionEnabled()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::DELETE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $resourceDeleteHandler->handle($resource, $repository)->willThrow(new DeleteHandlingException());

        $eventDispatcher->dispatchPostEvent(ResourceActions::DELETE, $configuration, $resource)->shouldNotBeCalled();

        $expectedView = View::create(null, 500);

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->deleteAction($request)->shouldReturn($response);
    }

    function it_deletes_a_resource_and_returns_204_for_non_html_requests(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        ViewHandlerInterface $viewHandler,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        EventDispatcherInterface $eventDispatcher,
        CsrfTokenManagerInterface $csrfTokenManager,
        ContainerInterface $container,
        ResourceControllerEvent $event,
        ResourceDeleteHandlerInterface $resourceDeleteHandler,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::DELETE)->willReturn('sylius.product.delete');
        $request->request = new ParameterBag(['_csrf_token' => 'xyz']);

        $container->has('security.csrf.token_manager')->willReturn(true);
        $container->get('security.csrf.token_manager')->willReturn($csrfTokenManager);
        $csrfTokenManager->isTokenValid(new CsrfToken(1, 'xyz'))->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.delete')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resource->getId()->willReturn(1);

        $configuration->isHtmlRequest()->willReturn(false);
        $configuration->isCsrfProtectionEnabled()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::DELETE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $resourceDeleteHandler->handle($resource, $repository)->shouldBeCalled();
        $eventDispatcher->dispatchPostEvent(ResourceActions::DELETE, $configuration, $resource)->shouldBeCalled();

        $expectedView = View::create(null, 204);

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->deleteAction($request)->shouldReturn($response);
    }

    function it_does_not_delete_a_resource_and_throws_http_exception_for_non_html_requests_stopped_via_event(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        CsrfTokenManagerInterface $csrfTokenManager,
        ContainerInterface $container,
        ResourceControllerEvent $event,
        ResourceDeleteHandlerInterface $resourceDeleteHandler,
        Request $request
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::DELETE)->willReturn('sylius.product.delete');
        $request->request = new ParameterBag(['_csrf_token' => 'xyz']);

        $container->has('security.csrf.token_manager')->willReturn(true);
        $container->get('security.csrf.token_manager')->willReturn($csrfTokenManager);
        $csrfTokenManager->isTokenValid(new CsrfToken(1, 'xyz'))->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.delete')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resource->getId()->willReturn(1);

        $configuration->isHtmlRequest()->willReturn(false);
        $configuration->isCsrfProtectionEnabled()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::DELETE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(true);
        $event->getMessage()->willReturn('Cannot delete this product.');
        $event->getErrorCode()->willReturn(500);

        $resourceDeleteHandler->handle($resource, $repository)->shouldNotBeCalled();

        $eventDispatcher->dispatchPostEvent(Argument::any())->shouldNotBeCalled();
        $flashHelper->addSuccessFlash(Argument::any())->shouldNotBeCalled();
        $flashHelper->addFlashFromEvent(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new HttpException(500, 'Cannot delete this product.'))
            ->during('deleteAction', [$request])
        ;
    }

    function it_throws_a_403_exception_if_csrf_token_is_invalid_during_delete_action(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        CsrfTokenManagerInterface $csrfTokenManager,
        ContainerInterface $container,
        ResourceControllerEvent $event,
        ResourceDeleteHandlerInterface $resourceDeleteHandler,
        Request $request
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::DELETE)->willReturn('sylius.product.delete');
        $request->request = new ParameterBag(['_csrf_token' => 'xyz']);

        $container->has('security.csrf.token_manager')->willReturn(true);
        $container->get('security.csrf.token_manager')->willReturn($csrfTokenManager);
        $csrfTokenManager->isTokenValid(new CsrfToken(1, 'xyz'))->willReturn(false);

        $authorizationChecker->isGranted($configuration, 'sylius.product.delete')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);
        $resource->getId()->willReturn(1);

        $configuration->isHtmlRequest()->willReturn(true);
        $configuration->isCsrfProtectionEnabled()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::DELETE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->shouldNotBeCalled();

        $resourceDeleteHandler->handle($resource, $repository)->shouldNotBeCalled();

        $eventDispatcher->dispatchPostEvent(Argument::any())->shouldNotBeCalled();
        $flashHelper->addSuccessFlash(Argument::any())->shouldNotBeCalled();
        $flashHelper->addFlashFromEvent(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new HttpException(403, 'Invalid csrf token.'))
            ->during('deleteAction', [$request])
        ;
    }

    function it_throws_a_403_exception_if_user_is_unauthorized_to_apply_state_machine_transition_on_resource(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker
    ): void {
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(false);

        $this
            ->shouldThrow(new AccessDeniedException())
            ->during('applyStateMachineTransitionAction', [$request])
        ;
    }

    function it_throws_a_404_exception_if_resource_is_not_found_when_trying_to_apply_state_machine_transition(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider
    ): void {
        $metadata->getHumanizedName()->willReturn('product');
        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn(null);

        $this
            ->shouldThrow(new NotFoundHttpException('The "product" has not been found'))
            ->during('applyStateMachineTransitionAction', [$request])
        ;
    }

    function it_does_not_apply_state_machine_transition_on_resource_if_not_applicable_and_returns_400_bad_request(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        ObjectManager $objectManager,
        StateMachineInterface $stateMachine,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        Request $request
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);

        $configuration->isHtmlRequest()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $stateMachine->can($configuration, $resource)->willReturn(false);

        $stateMachine->apply($configuration, $resource)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $eventDispatcher->dispatchPostEvent(Argument::any())->shouldNotBeCalled();
        $flashHelper->addSuccessFlash(Argument::any())->shouldNotBeCalled();
        $flashHelper->addFlashFromEvent(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new BadRequestHttpException())
            ->during('applyStateMachineTransitionAction', [$request])
        ;
    }

    function it_applies_state_machine_transition_to_resource_and_redirects_for_html_request(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RepositoryInterface $repository,
        ObjectManager $manager,
        SingleResourceProviderInterface $singleResourceProvider,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher,
        StateMachineInterface $stateMachine,
        ResourceUpdateHandlerInterface $resourceUpdateHandler,
        RequestConfiguration $configuration,
        ResourceInterface $resource,
        ResourceControllerEvent $event,
        ResourceControllerEvent $postEvent,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);

        $configuration->isHtmlRequest()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $stateMachine->can($configuration, $resource)->willReturn(true);
        $resourceUpdateHandler->handle($resource, $configuration, $manager)->shouldBeCalled();

        $flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource)->shouldBeCalled();

        $eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($postEvent);

        $postEvent->hasResponse()->willReturn(false);

        $redirectHandler->redirectToResource($configuration, $resource)->willReturn($redirectResponse);

        $this->applyStateMachineTransitionAction($request)->shouldReturn($redirectResponse);
    }

    function it_uses_response_from_post_apply_state_machine_transition_event_if_defined(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RepositoryInterface $repository,
        ObjectManager $manager,
        SingleResourceProviderInterface $singleResourceProvider,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher,
        StateMachineInterface $stateMachine,
        ResourceUpdateHandlerInterface $resourceUpdateHandler,
        RequestConfiguration $configuration,
        ResourceInterface $resource,
        ResourceControllerEvent $event,
        ResourceControllerEvent $postEvent,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);

        $configuration->isHtmlRequest()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $stateMachine->can($configuration, $resource)->willReturn(true);
        $resourceUpdateHandler->handle($resource, $configuration, $manager)->shouldBeCalled();

        $flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource)->shouldBeCalled();

        $eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($postEvent);

        $postEvent->hasResponse()->willReturn(true);
        $postEvent->getResponse()->willReturn($redirectResponse);

        $this->applyStateMachineTransitionAction($request)->shouldReturn($redirectResponse);
    }

    function it_does_not_apply_state_machine_transition_on_resource_and_redirects_for_html_requests_stopped_via_event(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        StateMachineInterface $stateMachine,
        ObjectManager $manager,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        Request $request,
        Response $redirectResponse
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);

        $configuration->isHtmlRequest()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(true);

        $manager->flush()->shouldNotBeCalled();
        $stateMachine->apply($resource)->shouldNotBeCalled();

        $eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource)->shouldNotBeCalled();
        $flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource)->shouldNotBeCalled();

        $event->hasResponse()->willReturn(false);

        $flashHelper->addFlashFromEvent($configuration, $event)->shouldBeCalled();
        $redirectHandler->redirectToResource($configuration, $resource)->willReturn($redirectResponse);

        $this->applyStateMachineTransitionAction($request)->shouldReturn($redirectResponse);
    }

    function it_does_not_apply_state_machine_transition_on_resource_and_return_event_response_for_html_requests_stopped_via_event(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        StateMachineInterface $stateMachine,
        ObjectManager $manager,
        RepositoryInterface $repository,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);

        $configuration->isHtmlRequest()->willReturn(true);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(true);

        $manager->flush()->shouldNotBeCalled();
        $stateMachine->apply($resource)->shouldNotBeCalled();

        $eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource)->shouldNotBeCalled();
        $flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource)->shouldNotBeCalled();

        $flashHelper->addFlashFromEvent($configuration, $event)->shouldBeCalled();

        $event->hasResponse()->willReturn(true);
        $event->getResponse()->willReturn($response);

        $this->applyStateMachineTransitionAction($request)->shouldReturn($response);
    }

    function it_applies_state_machine_transition_on_resource_and_returns_200_for_non_html_requests(
        MetadataInterface $metadata,
        ParameterBagInterface $parameterBag,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        ViewHandlerInterface $viewHandler,
        RepositoryInterface $repository,
        ObjectManager $manager,
        SingleResourceProviderInterface $singleResourceProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher,
        StateMachineInterface $stateMachine,
        ResourceUpdateHandlerInterface $resourceUpdateHandler,
        RequestConfiguration $configuration,
        ResourceInterface $resource,
        ResourceControllerEvent $event,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->getParameters()->willReturn($parameterBag);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $parameterBag->get('return_content', true)->willReturn(true);

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);

        $configuration->isHtmlRequest()->willReturn(false);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $stateMachine->can($configuration, $resource)->willReturn(true);
        $resourceUpdateHandler->handle($resource, $configuration, $manager)->shouldBeCalled();

        $eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource)->shouldBeCalled();

        $expectedView = View::create($resource, 200);

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->applyStateMachineTransitionAction($request)->shouldReturn($response);
    }

    function it_applies_state_machine_transition_on_resource_and_returns_204_for_non_html_requests_if_additional_option_added(
        MetadataInterface $metadata,
        ParameterBagInterface $parameterBag,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        ViewHandlerInterface $viewHandler,
        RepositoryInterface $repository,
        ObjectManager $manager,
        SingleResourceProviderInterface $singleResourceProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher,
        StateMachineInterface $stateMachine,
        ResourceUpdateHandlerInterface $resourceUpdateHandler,
        RequestConfiguration $configuration,
        ResourceInterface $resource,
        ResourceControllerEvent $event,
        Request $request,
        Response $response
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->getParameters()->willReturn($parameterBag);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $parameterBag->get('return_content', true)->willReturn(false);

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);

        $configuration->isHtmlRequest()->willReturn(false);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(false);

        $stateMachine->can($configuration, $resource)->willReturn(true);
        $resourceUpdateHandler->handle($resource, $configuration, $manager)->shouldBeCalled();

        $eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource)->shouldBeCalled();

        $expectedView = View::create(null, 204);

        $viewHandler->handle($configuration, Argument::that($this->getViewComparingCallback($expectedView)))->willReturn($response);

        $this->applyStateMachineTransitionAction($request)->shouldReturn($response);
    }

    function it_does_not_apply_state_machine_transition_resource_and_throws_http_exception_for_non_html_requests_stopped_via_event(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        RequestConfiguration $configuration,
        AuthorizationCheckerInterface $authorizationChecker,
        RepositoryInterface $repository,
        ObjectManager $objectManager,
        StateMachineInterface $stateMachine,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourceInterface $resource,
        FlashHelperInterface $flashHelper,
        EventDispatcherInterface $eventDispatcher,
        ResourceControllerEvent $event,
        Request $request
    ): void {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $requestConfigurationFactory->create($metadata, $request)->willReturn($configuration);
        $configuration->hasPermission()->willReturn(true);
        $configuration->getPermission(ResourceActions::UPDATE)->willReturn('sylius.product.update');

        $authorizationChecker->isGranted($configuration, 'sylius.product.update')->willReturn(true);
        $singleResourceProvider->get($configuration, $repository)->willReturn($resource);

        $configuration->isHtmlRequest()->willReturn(false);

        $eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource)->willReturn($event);
        $event->isStopped()->willReturn(true);
        $event->getMessage()->willReturn('Cannot approve this product.');
        $event->getErrorCode()->willReturn(500);

        $stateMachine->apply($configuration, $resource)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $eventDispatcher->dispatchPostEvent(Argument::any())->shouldNotBeCalled();
        $flashHelper->addSuccessFlash(Argument::any())->shouldNotBeCalled();
        $flashHelper->addFlashFromEvent(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new HttpException(500, 'Cannot approve this product.'))
            ->during('applyStateMachineTransitionAction', [$request])
        ;
    }

    /**
     * {@inheritdoc}
     */
    private function getViewComparingCallback(View $expectedView)
    {
        return function ($value) use ($expectedView) {
            if (!$value instanceof View) {
                return false;
            }

            // Need to unwrap phpspec's Collaborators to ensure proper comparison.
            $this->unwrapViewData($expectedView);
            $this->nullifyDates($value);
            $this->nullifyDates($expectedView);

            return
                $expectedView->getStatusCode() === $value->getStatusCode() &&
                $expectedView->getHeaders() === $value->getHeaders() &&
                $expectedView->getEngine() === $value->getEngine() &&
                $expectedView->getFormat() === $value->getFormat() &&
                $expectedView->getData() === $value->getData() &&
                $expectedView->getTemplate() === $value->getTemplate() &&
                $expectedView->getTemplateVar() === $value->getTemplateVar()
            ;
        };
    }

    /**
     * @param View $view
     */
    private function unwrapViewData(View $view)
    {
        $view->setData($this->unwrapIfCollaborator($view->getData()));
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function unwrapIfCollaborator($value)
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof Collaborator) {
            return $value->getWrappedObject();
        }

        if (is_array($value)) {
            foreach ($value as $key => $childValue) {
                $value[$key] = $this->unwrapIfCollaborator($childValue);
            }
        }

        return $value;
    }

    /**
     * @param View $view
     */
    private function nullifyDates(View $view)
    {
        $headers = $view->getHeaders();
        unset($headers['date']);
        $view->setHeaders($headers);
    }
}
