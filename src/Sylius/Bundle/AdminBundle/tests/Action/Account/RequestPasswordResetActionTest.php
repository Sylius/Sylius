<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\AdminBundle\Action\Account;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Action\Account\RequestPasswordResetAction;
use Sylius\Bundle\AdminBundle\Form\Model\PasswordResetRequest;
use Sylius\Bundle\AdminBundle\Form\RequestPasswordResetType;
use Sylius\Bundle\CoreBundle\Command\Admin\Account\RequestResetPasswordEmail;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class RequestPasswordResetActionTest extends TestCase
{
    private FormFactoryInterface&MockObject $formFactory;

    private MessageBusInterface&MockObject $messageBus;

    private MockObject&RequestStack $requestStack;

    private MockObject&RouterInterface $router;

    private Environment&MockObject $twig;

    private MockObject&Session $session;

    private FlashBagInterface&MockObject $flashBag;

    private RequestPasswordResetAction $requestPasswordResetAction;

    protected function setUp(): void
    {
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->session = $this->createMock(Session::class);
        $this->flashBag = $this->createMock(FlashBagInterface::class);

        $this->requestPasswordResetAction = new RequestPasswordResetAction(
            $this->formFactory,
            $this->messageBus,
            $this->requestStack,
            $this->router,
            $this->twig,
        );
    }

    public function testSendsResetPasswordRequestToMessageBus(): void
    {
        $form = $this->createMock(FormInterface::class);
        $request = $this->createMock(Request::class);
        $attributesBag = $this->createMock(ParameterBag::class);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($form)
        ;

        $form->expects($this->once())->method('handleRequest')->with($request)->willReturnSelf();
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail('sylius@example.com');

        $form->expects($this->once())->method('getData')->willReturn($passwordResetRequest);

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RequestResetPasswordEmail::class))
            ->willReturn(new Envelope(new \stdClass()))
        ;

        $this->flashBag
            ->expects($this->once())
            ->method('add')
            ->with('success', 'sylius.admin.request_reset_password.success')
        ;

        $this->session
            ->expects($this->once())
            ->method('getBag')
            ->with('flashes')
            ->willReturn($this->flashBag)
        ;

        $this->requestStack->expects($this->once())->method('getSession')->willReturn($this->session);

        $attributesBag
            ->expects($this->once())
            ->method('get')
            ->with('_sylius', [])
            ->willReturn(['redirect' => 'my_custom_route'])
        ;

        $request->attributes = $attributesBag;

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('my_custom_route')
            ->willReturn('/login')
        ;

        $response = $this->requestPasswordResetAction->__invoke($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/login', $response->getTargetUrl());
    }

    public function testItSendsResetPasswordRequestWhenSyliusRedirectParameterIsArray(): void
    {
        $form = $this->createMock(FormInterface::class);
        $request = $this->createMock(Request::class);
        $attributesBag = $this->createMock(ParameterBag::class);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($form)
        ;

        $form->expects($this->once())->method('handleRequest')->with($request)->willReturnSelf();
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail('sylius@example.com');

        $form->expects($this->once())->method('getData')->willReturn($passwordResetRequest);

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RequestResetPasswordEmail::class))
            ->willReturn(new Envelope(new \stdClass()))
        ;

        $this->flashBag
            ->expects($this->once())
            ->method('add')
            ->with('success', 'sylius.admin.request_reset_password.success')
        ;

        $this->session
            ->expects($this->once())
            ->method('getBag')
            ->with('flashes')
            ->willReturn($this->flashBag)
        ;

        $this->requestStack->expects($this->once())->method('getSession')->willReturn($this->session);

        $route = 'my_custom_route';
        $params = ['my_parameter' => 'my_value'];

        $attributesBag
            ->expects($this->once())
            ->method('get')
            ->with('_sylius', [])
            ->willReturn([
                'redirect' => [
                    'route' => $route,
                    'params' => $params,
                ],
            ])
        ;

        $request->attributes = $attributesBag;

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with($route, $params)
            ->willReturn('/login')
        ;

        $response = $this->requestPasswordResetAction->__invoke($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/login', $response->getTargetUrl());
    }

    public function testItRedirectsToDefaultRouteIfCustomOneIsNotDefined(): void
    {
        $form = $this->createMock(FormInterface::class);
        $request = $this->createMock(Request::class);
        $attributesBag = $this->createMock(ParameterBag::class);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($form)
        ;

        $form->expects($this->once())->method('handleRequest')->with($request)->willReturnSelf();
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(true);

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail('sylius@example.com');

        $form->expects($this->once())->method('getData')->willReturn($passwordResetRequest);

        $this->messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(RequestResetPasswordEmail::class))
            ->willReturn(new Envelope(new \stdClass()))
        ;

        $this->flashBag
            ->expects($this->once())
            ->method('add')
            ->with('success', 'sylius.admin.request_reset_password.success')
        ;

        $this->session
            ->expects($this->once())
            ->method('getBag')
            ->with('flashes')
            ->willReturn($this->flashBag)
        ;

        $this->requestStack->expects($this->once())->method('getSession')->willReturn($this->session);

        $attributesBag
            ->expects($this->once())
            ->method('get')
            ->with('_sylius', [])
            ->willReturn([])
        ;

        $request->attributes = $attributesBag;

        $this->router
            ->expects($this->once())
            ->method('generate')
            ->with('sylius_admin_login')
            ->willReturn('/login')
        ;

        $response = $this->requestPasswordResetAction->__invoke($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/login', $response->getTargetUrl());
    }

    public function testItRendersFormWithErrorsWhenRequestIsNotValid(): void
    {
        $form = $this->createMock(FormInterface::class);
        $formView = $this->createMock(FormView::class);
        $request = $this->createMock(Request::class);

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(RequestPasswordResetType::class)
            ->willReturn($form)
        ;

        $form->expects($this->once())->method('handleRequest')->with($request)->willReturnSelf();
        $form->expects($this->once())->method('isSubmitted')->willReturn(true);
        $form->expects($this->once())->method('isValid')->willReturn(false);

        $this->messageBus->expects($this->never())->method('dispatch');

        $form->expects($this->once())->method('createView')->willReturn($formView);

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with($this->isType('string'), ['form' => $formView])
            ->willReturn('responseContent')
        ;

        $response = $this->requestPasswordResetAction->__invoke($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('responseContent', $response->getContent());
    }
}
