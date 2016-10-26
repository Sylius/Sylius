<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SecurityController
{
    private static $DEFAULT_TARGET_PATH = [
        'admin' => 'sylius_admin_dashboard',
        'shop' => 'sylius_shop_homepage',
    ];

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param FormFactoryInterface $formFactory
     * @param EngineInterface $templatingEngine
     * @param AuthorizationChecker $authorizationChecker
     * @param TokenStorage $tokenStorage
     * @param Router $router
     */
    public function __construct(
        AuthenticationUtils $authenticationUtils,
        FormFactoryInterface $formFactory,
        EngineInterface $templatingEngine,
        AuthorizationChecker $authorizationChecker,
        TokenStorage $tokenStorage,
        Router $router
    ) {
        $this->authenticationUtils = $authenticationUtils;
        $this->formFactory = $formFactory;
        $this->templatingEngine = $templatingEngine;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        if ($redirect = $this->redirectIfLoggedIn()) {
            return $redirect;
        }

        $lastError = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $template = $request->attributes->get('_sylius[template]', 'SyliusUiBundle:Security:login.html.twig', true);
        $formType = $request->attributes->get('_sylius[form]', 'sylius_security_login', true);
        $form = $this->formFactory->createNamed('', $formType);

        return $this->templatingEngine->renderResponse($template, [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'last_error' => $lastError,
        ]);
    }

    /**
     * @param Request $request
     */
    public function checkAction(Request $request)
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    /**
     * @param Request $request
     */
    public function logoutAction(Request $request)
    {
        throw new \RuntimeException('You must configure the logout path to be handled by the firewall.');
    }

    /**
     * This will prevent users from going to the login page when they are already logged in.
     *
     * @return false|RedirectResponse
     */
    private function redirectIfLoggedIn()
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return false;
        }

        $token = $this->tokenStorage->getToken();
        $providerKey = $token->getProviderKey();

        if (!array_key_exists($providerKey, self::$DEFAULT_TARGET_PATH)) {
            return false;
        }

        $route = self::$DEFAULT_TARGET_PATH[$providerKey];

        return new RedirectResponse($this->router->generate($route));
    }
}
