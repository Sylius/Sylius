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

use Sylius\Bundle\CoreBundle\CommandDispatcher\ResetPasswordDispatcherInterface;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Bundle\UserBundle\Form\Model\PasswordReset;
use Sylius\Bundle\UserBundle\Form\Type\UserResetPasswordType;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @experimental
 */
final readonly class ResetPasswordAction
{
    public function __construct(
        private Environment $twig,
        private RouterInterface $router,
        private FormFactoryInterface $formFactory,
        private UserRepositoryInterface $userRepository,
        private TranslatorInterface $translator,
        private RequestStack $requestStack,
        private ResetPasswordDispatcherInterface $resetPasswordDispatcher,
        private string $tokenTtl,
    ) {
    }

    public function __invoke(Request $request, string $token, ?string $formType, ?string $template, ?string $redirect): Response
    {
        $formType ??= UserResetPasswordType::class;
        $redirect ??= 'sylius_shop_login';
        $template ??= '@SyliusShop/account/reset_password.html.twig';

        /** @var UserInterface|null $user */
        $user = $this->userRepository->findOneBy(['passwordResetToken' => $token]);

        if (null === $user) {
            throw new NotFoundHttpException('Token not found.');
        }

        $lifetime = new \DateInterval($this->tokenTtl);

        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            $this->addErrorNotification();

            return new RedirectResponse($this->router->generate($redirect));
        }

        $passwordReset = new PasswordReset();
        $form = $this->formFactory->create($formType, $passwordReset);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->resetPasswordDispatcher->dispatch($token, $passwordReset->getPassword());

            $this->addSuccessNotification();

            return new RedirectResponse($this->router->generate($redirect));
        }

        return new Response($this->twig->render($template, [
            'form' => $form->createView(),
            'user' => $user,
        ]));
    }

    private function addSuccessNotification(): void
    {
        FlashBagProvider::getFlashBag($this->requestStack)->add('success', $this->translator->trans('sylius.user.reset_password', [], 'flashes'));
    }

    private function addErrorNotification(): void
    {
        FlashBagProvider::getFlashBag($this->requestStack)->add('error', $this->translator->trans('sylius.user.expire_password_reset_token', [], 'flashes'));
    }
}
