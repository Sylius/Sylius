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
use Sylius\Bundle\AdminBundle\Form\RequestPasswordResetType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class GetRequestPasswordResetActionSpec extends ObjectBehavior
{
    public function let(
        Environment $twig,
        FormFactoryInterface $formFactory
    ): void {
        $this->beConstructedWith($twig, $formFactory);
    }

    public function it_renders_the_template_with_request_password_reset_form(
        Environment $twig,
        FormFactoryInterface $formFactory,
        FormInterface $form,
        FormView $formView
    ): void {
        $formFactory->create(RequestPasswordResetType::class)->shouldBeCalled()->willReturn($form);
        $form->createView()->shouldBeCalled()->willReturn($formView);

        $twig
            ->render('@SyliusAdmin/Security/requestPasswordReset.html.twig', [
                'form' => $formView,
            ])
            ->shouldBeCalled()
            ->willReturn('some template body')
        ;

        $response = $this->__invoke();
        $response->shouldBeAnInstanceOf(Response::class);
        $response->getContent()->shouldReturn('some template body');
    }
}
