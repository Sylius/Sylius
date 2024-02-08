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

namespace Sylius\Bundle\UiBundle\Controller;

use Sylius\Bundle\UiBundle\Form\Type\SecurityLoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

final class SecurityController
{
    public function __construct(
        private AuthenticationUtils $authenticationUtils,
        private FormFactoryInterface $formFactory,
        private Environment $templatingEngine,
        private AuthorizationCheckerInterface $authorizationChecker,
        private RouterInterface $router,
    ) {
    }

    public function loginAction(Request $request): Response
    {
        $alreadyLoggedInRedirectRoute = $request->attributes->get('_sylius', [])['logged_in_route'] ?? null;

        if ($alreadyLoggedInRedirectRoute && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->router->generate($alreadyLoggedInRedirectRoute));
        }

        $lastError = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $options = $request->attributes->get('_sylius', []);

        $template = $options['template'] ?? '@SyliusUi/Security/login.html.twig';
        $formType = $options['form'] ?? SecurityLoginType::class;
        $form = $this->formFactory->createNamed('', $formType);

        return new Response($this->templatingEngine->render($template, [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'last_error' => $lastError,
        ]));
    }

    public function checkAction(Request $request): void
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    public function logoutAction(Request $request): void
    {
        throw new \RuntimeException('You must configure the logout path to be handled by the firewall.');
    }
}
