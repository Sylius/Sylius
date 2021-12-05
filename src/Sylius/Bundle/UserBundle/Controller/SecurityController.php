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

namespace Sylius\Bundle\UserBundle\Controller;

use Sylius\Bundle\UserBundle\Form\Type\UserLoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Webmozart\Assert\Assert;

class SecurityController extends AbstractController
{
    private ?AuthenticationUtils $authenticationUtils;

    private ?FormFactoryInterface $formFactory;

    public function __construct(?AuthenticationUtils $authenticationUtils = null, ?FormFactoryInterface $formFactory = null)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->formFactory = $formFactory;

        if ($this->authenticationUtils === null) {
            @trigger_error(sprintf('Not passing a $authenticationUtils to %s constructor is deprecated since Sylius 1.11 and will be prohibited in Sylius 2.0.', self::class), \E_USER_DEPRECATED);
        }

        if ($this->formFactory === null) {
            @trigger_error(sprintf('Not passing a $formFactory to %s constructor is deprecated since Sylius 1.11 and will be prohibited in Sylius 2.0.', self::class), \E_USER_DEPRECATED);
        }
    }

    /**
     * Login form action.
     *
     * @psalm-suppress DeprecatedMethod
     */
    public function loginAction(Request $request): Response
    {
        if ($this->authenticationUtils !== null) {
            $authenticationUtils = $this->authenticationUtils;
        } else {
            $authenticationUtils = $this->get('security.authentication_utils');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $options = $request->attributes->get('_sylius');

        $template = $options['template'] ?? null;
        Assert::notNull($template, 'Template is not configured.');

        $formType = $options['form'] ?? UserLoginType::class;

        if ($this->formFactory !== null) {
            $form = $this->formFactory->createNamed('', $formType);
        } else {
            $form = $this->get('form.factory')->createNamed('', $formType);
        }

        return $this->render($template, [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Login check action. This action should never be called.
     */
    public function checkAction(Request $request): Response
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    /**
     * Logout action. This action should never be called.
     */
    public function logoutAction(Request $request): Response
    {
        throw new \RuntimeException('You must configure the logout path to be handled by the firewall.');
    }
}
