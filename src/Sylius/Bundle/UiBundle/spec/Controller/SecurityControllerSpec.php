<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UiBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\Controller\SecurityController;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SecurityControllerSpec extends ObjectBehavior
{
    function let(AuthenticationUtils $authenticationUtils, FormFactoryInterface $formFactory, EngineInterface $templatingEngine)
    {
        $this->beConstructedWith($authenticationUtils, $formFactory, $templatingEngine);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SecurityController::class);
    }

    function it_renders_login_form(
        Request $request,
        ParameterBag $requestAttributes,
        AuthenticationUtils $authenticationUtils,
        FormFactoryInterface $formFactory,
        Form $form,
        FormView $formView,
        EngineInterface $templatingEngine,
        Response $response
    ) {
        $authenticationUtils->getLastAuthenticationError()->willReturn('Bad credentials.');
        $authenticationUtils->getLastUsername()->willReturn('john.doe');

        $request->attributes = $requestAttributes;
        $requestAttributes->get('_sylius')->willReturn([
            'template' => 'CustomTemplateName',
            'form' => 'custom_form_type',
        ]);

        $formFactory->createNamed('', 'custom_form_type')->willReturn($form);
        $form->createView()->willReturn($formView);

        $templatingEngine
            ->renderResponse('CustomTemplateName', [
                'form' => $formView,
                'last_username' => 'john.doe',
                'last_error' => 'Bad credentials.',
            ])
            ->willReturn($response)
        ;

        $this->loginAction($request)->shouldReturn($response);
    }

    function it_throws_an_exception_when_check_action_is_accessed(Request $request)
    {
        $this
            ->shouldThrow(new \RuntimeException('You must configure the check path to be handled by the firewall.'))
            ->during('checkAction', [$request]);
    }

    function it_throws_an_exception_when_logout_action_is_accessed(Request $request)
    {
        $this
            ->shouldThrow(new \RuntimeException('You must configure the logout path to be handled by the firewall.'))
            ->during('logoutAction', [$request]);
    }
}
