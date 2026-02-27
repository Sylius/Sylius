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

namespace Sylius\Bundle\ShopBundle\Controller;

use Sylius\Bundle\CoreBundle\Command\Shop\Account\RequestResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Bundle\UserBundle\Form\Model\PasswordResetRequest;
use Sylius\Bundle\UserBundle\Form\Type\UserRequestPasswordResetType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @experimental
 */
final readonly class RequestPasswordResetTokenAction
{
    public function __construct(
        private Environment $twig,
        private RouterInterface $router,
        private FormFactoryInterface $formFactory,
        private MessageBusInterface $messageBus,
        private TranslatorInterface $translator,
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(Request $request, ?string $formType, ?string $template, ?string $redirect): Response
    {
        $formType ??= UserRequestPasswordResetType::class;
        $redirect ??= 'sylius_shop_login';
        $template ??= '@SyliusShop/account/forgotten_password.html.twig';

        $passwordReset = new PasswordResetRequest();
        $form = $this->formFactory->create($formType, $passwordReset);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $requestPasswordResetMessage = new RequestResetPasswordEmail(
                $passwordReset->getEmail(),
            );

            $this->messageBus->dispatch($requestPasswordResetMessage);

            $this->addSuccessNotification();

            return new RedirectResponse($this->router->generate($redirect));
        }

        return new Response($this->twig->render($template, [
            'form' => $form->createView(),
        ]));
    }

    private function addSuccessNotification(): void
    {
        FlashBagProvider::getFlashBag($this->requestStack)->add('success', $this->translator->trans('sylius.user.reset_password_request', [], 'flashes'));
    }
}
