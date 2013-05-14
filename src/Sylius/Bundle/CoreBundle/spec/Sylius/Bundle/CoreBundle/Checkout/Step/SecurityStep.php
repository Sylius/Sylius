<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Checkout\Step;

use PHPSpec2\ObjectBehavior;

/**
 * Checkout security step spec.
 */
class SecurityStep extends ObjectBehavior
{
    /**
     * @param  Symfony\Component\DependencyInjection\ContainerInterface            $container
     * @param  Symfony\Component\Form\Form                                         $form
     * @param  Symfony\Component\HttpFoundation\Request                            $request
     * @param  Symfony\Bundle\FrameworkBundle\Templating\EngineInterface           $templating
     * @param  FOS\UserBundle\Model\UserManagerInterface                           $userManager
     * @param  Symfony\Component\Security\SecurityContextInterface                 $security
     */
    function let($container, $form, $request, $templating, $userManager, $security)
    {
        $container->get('fos_user.registration.form')->willReturn($form);
        $container->get('request')->willReturn($request);
        $container->get('templating')->willReturn($templating);
        $container->get('fos_user.user_manager')->willReturn($userManager);
        $container->get('security.context')->willReturn($security);

        $this->setContainer($container);

    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Checkout\Step\SecurityStep');
    }

    function it_extends_checkout_step()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Checkout\Step\SecurityStep');
    }

    /**
     * @param Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface $context
     */
    function its_forwardAction_render_step_without_register($context, $templating, $request)
    {
        $request->isMethod('POST')->willReturn(false);
        $templating->renderResponse(ANY_ARGUMENTS)->shouldBeCalled();

        $this->forwardAction($context);
    }

    /**
     * @param Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface $context
     */
    function its_forwardAction_render_step_with_wrong_register($context, $templating, $request, $form)
    {
        $request->isMethod('POST')->willReturn(true);
        $form->bind($request)->willReturn($form);
        $form->isValid()->willReturn(false);

        $templating->renderResponse(ANY_ARGUMENTS)->shouldBeCalled();
        $this->forwardAction($context);
    }

    /**
     * @param Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface $context
     * @param Sylius\Bundle\CoreBundle\Entity\User                             $user
     */
    function its_forwardAction_complete_and_register_new_user($context, $templating, $request, $form, $user, $userManager, $container, $security)
    {
        $request->isMethod('POST')->willReturn(true);
        $form->bind($request)->willReturn($form);
        $form->isValid()->willReturn(true);

        $form->getData()->shouldBeCalled()->willReturn($user);
        $user->getRoles()->willReturn(array());
        $userManager->updateUser($user)->shouldBeCalled();
        $container->getParameter('fos_user.firewall_name')->shouldBeCalled()->willReturn('provider_KEY');

        $security->setToken(ANY_ARGUMENT)->shouldBeCalled();

        $templating->renderResponse(ANY_ARGUMENTS)->shouldNotBeCalled();
        $response = $this->forwardAction($context);
        $response->shouldHaveType('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }
}
