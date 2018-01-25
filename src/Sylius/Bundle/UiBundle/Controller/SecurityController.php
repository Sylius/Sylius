<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\Controller;

use Sylius\Bundle\UiBundle\Form\Type\SecurityLoginType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController
{
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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param FormFactoryInterface $formFactory
     * @param EngineInterface $templatingEngine
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param RouterInterface $router
     */
    public function __construct(
        AuthenticationUtils $authenticationUtils,
        FormFactoryInterface $formFactory,
        EngineInterface $templatingEngine,
        AuthorizationCheckerInterface $authorizationChecker,
        RouterInterface $router
    ) {
        $this->authenticationUtils = $authenticationUtils;
        $this->formFactory = $formFactory;
        $this->templatingEngine = $templatingEngine;
        $this->authorizationChecker = $authorizationChecker;
        $this->router = $router;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request): Response
    {
        $alreadyLoggedInRedirectRoute = $request->attributes->get('_sylius')['logged_in_route'] ?? null;

        if ($alreadyLoggedInRedirectRoute && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->router->generate($alreadyLoggedInRedirectRoute));
        }

        $lastError = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $options = $request->attributes->get('_sylius');

        $template = $options['template'] ?? '@SyliusUi/Security/login.html.twig';
        $formType = $options['form'] ?? SecurityLoginType::class;
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
    public function checkAction(Request $request): void
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    /**
     * @param Request $request
     */
    public function logoutAction(Request $request): void
    {
        throw new \RuntimeException('You must configure the logout path to be handled by the firewall.');
    }
}
