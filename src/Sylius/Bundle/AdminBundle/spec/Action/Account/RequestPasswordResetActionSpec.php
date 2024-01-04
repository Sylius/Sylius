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

namespace spec\Sylius\Bundle\AdminBundle\Action\Account;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AdminBundle\Form\Model\PasswordResetRequest;
use Sylius\Bundle\AdminBundle\Form\RequestPasswordResetType;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\RequestResetPasswordEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
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

final class RequestPasswordResetActionSpec extends ObjectBehavior
{
    public function let(
        FormFactoryInterface $formFactory,
        MessageBusInterface $messageBus,
        RequestStack $requestStack,
        RouterInterface $router,
        Environment $twig,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $this->beConstructedWith($formFactory, $messageBus, $requestStack, $router, $twig);

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);
    }

    public function it_sends_reset_password_request_to_message_bus(
        FormFactoryInterface $formFactory,
        MessageBusInterface $messageBus,
        FlashBagInterface $flashBag,
        RouterInterface $router,
        FormInterface $form,
        Request $request,
        ParameterBagInterface $attributesBag,
    ): void {
        $formFactory
            ->create(RequestPasswordResetType::class)
            ->shouldBeCalled()
            ->willReturn($form)
        ;

        $form->handleRequest($request)->willReturn($form)->shouldBeCalled();
        $form->isSubmitted()->shouldBeCalled()->willReturn(true);
        $form->isValid()->shouldBeCalled()->willReturn(true);

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail('sylius@example.com');
        $form->getData()->shouldBeCalled()->willReturn($passwordResetRequest);

        $messageBus
            ->dispatch(Argument::type(RequestResetPasswordEmail::class))
            ->shouldBeCalled()
            ->willReturn(new Envelope(new \stdClass()))
        ;

        $flashBag->add('success', 'sylius.admin.request_reset_password.success')->shouldBeCalled();

        $attributesBag->get('_sylius', [])->shouldBeCalled()->willReturn([
            'redirect' => 'my_custom_route',
        ]);
        $request->attributes = $attributesBag->getWrappedObject();

        $router->generate('my_custom_route')->shouldBeCalled()->willReturn('/login');

        $response = $this->__invoke($request);
        $response->shouldBeAnInstanceOf(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn('/login');
    }

    public function it_is_able_to_send_reset_password_request_when_sylius_redirect_parameter_is_an_array(
        FormFactoryInterface $formFactory,
        MessageBusInterface $messageBus,
        FlashBagInterface $flashBag,
        RouterInterface $router,
        FormInterface $form,
        Request $request,
        ParameterBagInterface $attributesBag,
    ): void {
        $formFactory->create(RequestPasswordResetType::class)->willReturn($form);

        $form->handleRequest($request)->willReturn($form);
        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail('sylius@example.com');
        $form->getData()->shouldBeCalled()->willReturn($passwordResetRequest);

        $messageBus->dispatch(Argument::type(RequestResetPasswordEmail::class))->willReturn(new Envelope(new \stdClass()));

        $flashBag->add('success', 'sylius.admin.request_reset_password.success')->shouldBeCalled();

        $route = 'my_custom_route';
        $parameters = [
            'my_parameter' => 'my_value',
        ];
        $attributesBag->get('_sylius', [])->shouldBeCalled()->willReturn([
            'redirect' => [
                'route' => $route,
                'params' => $parameters,
            ],
        ]);
        $request->attributes = $attributesBag->getWrappedObject();

        $router->generate('my_custom_route', ['my_parameter' => 'my_value'])->shouldBeCalled()->willReturn('/login');

        $response = $this->__invoke($request);
        $response->shouldBeAnInstanceOf(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn('/login');
    }

    public function it_redirects_to_default_route_if_custom_one_is_not_defined(
        FormFactoryInterface $formFactory,
        MessageBusInterface $messageBus,
        FlashBagInterface $flashBag,
        RouterInterface $router,
        FormInterface $form,
        Request $request,
        ParameterBagInterface $attributesBag,
    ): void {
        $formFactory->create(RequestPasswordResetType::class)->willReturn($form);

        $form->handleRequest($request)->willReturn($form);
        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(true);

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail('sylius@example.com');
        $form->getData()->shouldBeCalled()->willReturn($passwordResetRequest);

        $messageBus->dispatch(Argument::type(RequestResetPasswordEmail::class))->willReturn(new Envelope(new \stdClass()));

        $flashBag->add('success', 'sylius.admin.request_reset_password.success')->shouldBeCalled();

        $attributesBag->get('_sylius', [])->shouldBeCalled()->willReturn([]);
        $request->attributes = $attributesBag->getWrappedObject();

        $router->generate('sylius_admin_login')->shouldBeCalled()->willReturn('/login');

        $response = $this->__invoke($request);
        $response->shouldBeAnInstanceOf(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn('/login');
    }

    public function it_renders_form_with_errors_when_its_request_is_not_valid(
        FormFactoryInterface $formFactory,
        MessageBusInterface $messageBus,
        Environment $twig,
        FormInterface $form,
        FormView $formView,
        Request $request,
    ): void {
        $formFactory->create(RequestPasswordResetType::class)->willReturn($form);

        $form->handleRequest($request)->willReturn($form);
        $form->isSubmitted()->willReturn(true);
        $form->isValid()->willReturn(false);

        $messageBus->dispatch(Argument::any())->shouldNotBeCalled();

        $form->createView()->willReturn($formView);

        $twig->render(Argument::type('string'), [
            'form' => $formView,
        ])->shouldBeCalled()->willReturn('responseContent');

        $response = $this->__invoke($request);
        $response->shouldBeAnInstanceOf(Response::class);
        $response->getContent()->shouldReturn('responseContent');
    }
}
