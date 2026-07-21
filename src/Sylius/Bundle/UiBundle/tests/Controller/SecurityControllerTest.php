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

namespace Tests\Sylius\Bundle\UiBundle\Controller;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UiBundle\Controller\SecurityController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

#[AllowMockObjectsWithoutExpectations]
final class SecurityControllerTest extends TestCase
{
    private AuthenticationUtils&MockObject $authenticationUtils;

    private FormFactoryInterface&MockObject $formFactory;

    private Environment&MockObject $templatingEngine;

    private AuthorizationCheckerInterface&MockObject $authorizationChecker;

    private MockObject&RouterInterface $router;

    private Request $request;

    private SecurityController $securityController;

    protected function setUp(): void
    {
        $this->authenticationUtils = $this->createMock(AuthenticationUtils::class);
        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->templatingEngine = $this->createMock(Environment::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->request = new Request();

        $this->securityController = new SecurityController(
            $this->authenticationUtils,
            $this->formFactory,
            $this->templatingEngine,
            $this->authorizationChecker,
            $this->router,
        );
    }

    public function testRendersLoginForm(): void
    {
        $this->request->attributes->set('_sylius', [
            'template' => 'CustomTemplateName',
            'form' => 'custom_form_type',
        ]);
        /** @var Form&MockObject $form */
        $form = $this->createMock(Form::class);
        /** @var FormView&MockObject $formView */
        $formView = $this->createMock(FormView::class);
        /** @var AuthenticationException&MockObject $authenticationException */
        $authenticationException = $this->createMock(AuthenticationException::class);

        $this->authorizationChecker->expects($this->never())->method('isGranted')->with('IS_AUTHENTICATED_FULLY')->willReturn(false);
        $this->authenticationUtils->expects($this->once())->method('getLastAuthenticationError')->willReturn($authenticationException);
        $this->authenticationUtils->expects($this->once())->method('getLastUsername')->willReturn('john.doe');

        $this->formFactory->expects($this->once())->method('createNamed')->with('', 'custom_form_type')->willReturn($form);
        $form->expects($this->once())->method('createView')->willReturn($formView);
        $this->templatingEngine->expects($this->once())->method('render')->with('CustomTemplateName', [
            'form' => $formView,
            'last_username' => 'john.doe',
            'last_error' => $authenticationException,
        ])
            ->willReturn('content')
        ;
        $this->assertSame('content', $this->securityController->loginAction($this->request)->getContent());
    }

    public function testRedirectsWhenUserIsLoggedIn(): void
    {
        $this->request->attributes->set('_sylius', ['logged_in_route' => 'foo_bar']);

        $this->authorizationChecker->expects($this->once())->method('isGranted')->with('IS_AUTHENTICATED_FULLY')->willReturn(true);
        $this->router->expects($this->once())->method('generate')->with('foo_bar')->willReturn('/login');

        $this->assertInstanceOf(RedirectResponse::class, $this->securityController->loginAction($this->request));
    }

    public function testThrowsAnExceptionWhenCheckActionIsAccessed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You must configure the check path to be handled by the firewall.');

        $this->securityController->checkAction($this->request);
    }

    public function testThrowsAnExceptionWhenLogoutActionIsAccessed(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('You must configure the logout path to be handled by the firewall.');

        $this->securityController->logoutAction($this->request);
    }
}
