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

use Sylius\Bundle\CoreBundle\Command\Shop\Account\ChangeShopUserPassword;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Bundle\ShopBundle\Form\Type\UserChangePasswordType;
use Sylius\Bundle\UserBundle\Form\Model\ChangePassword;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
final readonly class ChangePasswordAction
{
    public function __construct(
        private Environment $twig,
        private TokenStorageInterface $tokenStorage,
        private FormFactoryInterface $formFactory,
        private MessageBusInterface $messageBus,
        private RouterInterface $router,
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(Request $request, ?string $formType = null, ?string $template = null, ?string $redirect = null): Response
    {
        $template ??= '@SyliusShop/account/change_password.html.twig';
        $formType ??= UserChangePasswordType::class;
        $redirect ??= 'sylius_shop_account_dashboard';

        $user = $this->getUser();

        $changePassword = new ChangePassword();
        $form = $this->formFactory->create($formType, $changePassword);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $this->messageBus->dispatch(new ChangeShopUserPassword($changePassword->getNewPassword(), $user->getId()));

            $this->addSuccessNotification();

            return new RedirectResponse($this->router->generate($redirect));
        }

        return new Response($this->twig->render($template, [
            'form' => $form->createView(),
        ]));
    }

    private function getUser(): UserInterface
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (null === $user) {
            throw new AccessDeniedException('You have to be registered user to access this section.');
        }

        Assert::isInstanceOf($user, UserInterface::class);

        return $user;
    }

    private function addSuccessNotification(): void
    {
        FlashBagProvider::getFlashBag($this->requestStack)->add('success', 'sylius.user.change_password');
    }
}
