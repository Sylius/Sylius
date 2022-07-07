<?php

/*
 *  This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;

final class PostRequestPasswordResetActionSpec extends ObjectBehavior
{
    public function let(
        FormFactoryInterface $formFactory,
        MessageBusInterface $messageBus,
        FlashBagInterface $flashBag,
        RouterInterface $router
    ): void {
        $this->beConstructedWith($formFactory, $messageBus, $flashBag, $router);
    }

    public function it_sends_reset_password_request_to_message_bus(
        FormFactoryInterface $formFactory,
        MessageBusInterface $messageBus,
        FlashBagInterface $flashBag,
        RouterInterface $router,
        FormInterface $form,
        Request $request
    ): void
    {
        $formFactory
            ->create(RequestPasswordResetType::class, Argument::type(PasswordResetRequest::class))
            ->shouldBeCalled()
            ->willReturn($form)
        ;

        $form->handleRequest($request)->shouldBeCalled();
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

        $flashBag->set('success', 'sylius.admin.request_reset_password.success')->shouldBeCalled();

        $router->generate('sylius_admin_login')->shouldBeCalled()->willReturn('/login');

        $response = $this->__invoke($request);
        $response->shouldBeAnInstanceOf(RedirectResponse::class);
        $response->getTargetUrl()->shouldReturn('/login');
    }
}
