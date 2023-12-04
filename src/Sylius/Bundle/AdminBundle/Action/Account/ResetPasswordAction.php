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

namespace Sylius\Bundle\AdminBundle\Action\Account;

use Sylius\Bundle\AdminBundle\Form\Model\PasswordReset;
use Sylius\Bundle\AdminBundle\Form\Type\ResetPasswordType;
use Sylius\Bundle\CoreBundle\MessageDispatcher\ResetPasswordDispatcherInterface;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class ResetPasswordAction
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private ResetPasswordDispatcherInterface $resetPasswordDispatcher,
        private FlashBagInterface|RequestStack $requestStackOrFlashBag,
        private RouterInterface $router,
        private Environment $twig,
    ) {
        if ($this->requestStackOrFlashBag instanceof FlashBagInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.12',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in Sylius 2.0. Pass an instance of %s instead.',
                FlashBagInterface::class,
                self::class,
                RequestStack::class,
            );
        }
    }

    public function __invoke(Request $request, string $token): Response
    {
        $form = $this->formFactory->create(ResetPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PasswordReset $passwordReset */
            $passwordReset = $form->getData();

            $this->resetPasswordDispatcher->dispatch($token, $passwordReset->getPassword());

            FlashBagProvider
                ::getFlashBag($this->requestStackOrFlashBag)
                ->add('success', 'sylius.admin.password_reset.success')
            ;

            $attributes = $request->attributes->get('_sylius', []);
            $redirect = $attributes['redirect'] ?? 'sylius_admin_login';

            if (is_array($redirect)) {
                return new RedirectResponse(
                    $redirect['route'] ?? 'sylius_admin_login',
                    $redirect['params'] ?? [],
                );
            }

            return new RedirectResponse($this->router->generate($redirect));
        }

        return new Response(
            $this->twig->render('@SyliusAdmin/Security/resetPassword.html.twig', [
                'form' => $form->createView(),
            ]),
        );
    }
}
