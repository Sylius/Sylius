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

use Sylius\Bundle\AdminBundle\Form\Type\ResetPasswordType;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class RenderResetPasswordPageAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private FormFactoryInterface $formFactory,
        private FlashBagInterface|RequestStack $requestStackOrFlashBag,
        private RouterInterface $router,
        private Environment $twig,
        private string $tokenTtl,
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
        /** @var AdminUserInterface|null $admin */
        $admin = $this->userRepository->findOneBy(['passwordResetToken' => $token]);
        if (null === $admin) {
            throw new NotFoundHttpException('Token not found');
        }

        $lifetime = new \DateInterval($this->tokenTtl);

        if (!$admin->isPasswordRequestNonExpired($lifetime)) {
            return $this->handleExpiredPasswordRequest($request);
        }

        $form = $this->formFactory->create(ResetPasswordType::class);

        return new Response(
            $this->twig->render('@SyliusAdmin/Security/resetPassword.html.twig', [
                'form' => $form->createView(),
            ]),
        );
    }

    private function handleExpiredPasswordRequest(Request $request): RedirectResponse
    {
        FlashBagProvider
            ::getFlashBag($this->requestStackOrFlashBag)
            ->add('error', 'sylius.admin.password_reset.token_expired')
        ;

        $attributes = $request->attributes->get('_sylius', []);
        $redirect = $attributes['redirect'] ?? 'sylius_admin_login';

        if (is_array($redirect)) {
            return new RedirectResponse($this->router->generate(
                $redirect['route'] ?? 'sylius_admin_login',
                $redirect['params'] ?? [],
            ));
        }

        return new RedirectResponse($this->router->generate($redirect));
    }
}
