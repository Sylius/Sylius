<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\UserBundle\Form\Model\ChangePassword;
use Sylius\Bundle\UserBundle\Form\Model\PasswordReset;
use Sylius\Bundle\UserBundle\Form\Type\UserChangePasswordType;
use Sylius\Bundle\UserBundle\Form\Type\UserRequestPasswordResetType;
use Sylius\Bundle\UserBundle\Form\Type\UserResetPasswordType;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\TokenProviderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserController extends ResourceController
{
    public function changePasswordAction(Request $request)
    {
        $this->validateAccess();
        $user = $this->getUser();
        $changePassword = new ChangePassword();
        $form = $this->createResourceForm(new UserChangePasswordType(), $changePassword);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $encoderFactory = $this->get('security.encoder_factory');

            $encoder = $encoderFactory->getEncoder($user);
            $validPassword = $encoder->isPasswordValid(
                $user->getPassword(),
                $changePassword->getCurrentPassword(),
                $user->getSalt()
            );

            if ($validPassword) {
                $user->setPlainPassword($changePassword->getNewPassword());

                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch(UserEvents::PASSWORD_RESET_SUCCESS, new GenericEvent($user));

                $this->domainManager->update($user);

                if ($this->config->isApiRequest()) {
                    return $this->handleView($this->view($user, 204));
                }
                $this->addFlash('success', 'sylius.account.password.change_success');

                return new RedirectResponse($this->generateUrl('sylius_account_homepage'));
            }

            $this->addFlash('error', 'sylius.account.password.invalid');
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        return $this->render(
            'SyliusWebBundle:Frontend/Account:changePassword.html.twig',
            array(
                'form'  => $form->createView(),
            )
        );
    }

    public function requestPasswordResetTokenAction(Request $request)
    {
        $generator = $this->get('sylius.user.token_provider');

        return $this->prepereResetPasswordRequest($request, $generator, UserEvents::REQUEST_RESET_PASSWORD_TOKEN);
    }

    public function requestPasswordResetPinAction(Request $request)
    {
        $generator = $this->get('sylius.user.pin_provider');

        return $this->prepereResetPasswordRequest($request, $generator, UserEvents::REQUEST_RESET_PASSWORD_PIN);
    }

    public function resetPasswordAction(Request $request)
    {
        $user = $this->getRepository()->findOneBy(array('confirmationToken' => $request->get('token')));
        if (null === $user) {
            throw new NotFoundHttpException('This token does not exist');
        }

        $lifetime = new \DateInterval($this->container->getParameter('sylius.user.resetting.token_ttl'));
        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            return $this->handleExpiredToken($request, $user);
        }

        $changePassword = new ChangePassword();
        $form = $this->createResourceForm(new UserResetPasswordType(), $changePassword);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $user->setPlainPassword($changePassword->getNewPassword());
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);

            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(UserEvents::PASSWORD_RESET_SUCCESS, new GenericEvent($user));

            $this->domainManager->update($user);

            if ($this->config->isApiRequest()) {
                return $this->handleView($this->view($user, 204));
            }

            $this->addFlash('success', 'sylius.account.password.change_success');

            return new RedirectResponse($this->generateUrl('sylius_user_security_login'));
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        return $this->render(
            'SyliusWebBundle:Frontend/Account:resetPassword.html.twig',
            array(
                'form' => $form->createView(),
                'user' => $user,
            )
        );
    }

    protected function prepereResetPasswordRequest(Request $request, TokenProviderInterface $generator, $senderEvent)
    {
        $passwordReset = new PasswordReset();
        $form = $this->createResourceForm(new UserRequestPasswordResetType(), $passwordReset);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $user = $this->getRepository()->findOneByEmail($passwordReset->getEmail());

            if (null !== $user) {

                $user->setConfirmationToken($generator->generateUniqueToken());
                $user->setPasswordRequestedAt(new \DateTime());

                $this->domainManager->update($user);

                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch($senderEvent, new GenericEvent($user));

                if ($this->config->isApiRequest()) {
                    return $this->handleView($this->view($user, 204));
                }

                $this->addFlash('success', 'sylius.account.password.reset.success');

                return $this->render(
                    'SyliusWebBundle:Frontend/Account:requestPasswordReset.html.twig',
                    array(
                        'form'  => $form->createView(),
                    )
                );
            }

            $this->addFlash('error', 'sylius.account.email.not_exist');
            $this->addFlash('error', 'sylius.account.password.reset.failed');
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        return $this->render(
            'SyliusWebBundle:Frontend/Account:requestPasswordReset.html.twig',
            array(
                'form'  => $form->createView(),
            )
        );
    }

    protected function addFlash($type, $message)
    {
        $translator = $this->get('translator');
        $this->get('session')->getFlashBag()->add($type, $translator->trans($message, array(), 'flashes'));
    }

    protected function createResourceForm($type, $resource)
    {
        if ($this->config->isApiRequest()) {
            return $this->container->get('form.factory')->createNamed('', $type, $resource, array('csrf_protection' => false));
        }

        return $this->createForm($type, $resource);
    }

    /**
     * @param  Request          $request
     * @param  UserInterface    $user
     * @return RedirectResponse
     */
    protected function handleExpiredToken(Request $request, UserInterface $user)
    {
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);

        $this->domainManager->update($user);

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($user, 400));
        }

        $this->addFlash('error', 'sylius.account.password.token_expired');

        $url = $this->generateUrl('sylius_user_request_password_reset_token');

        if (is_numeric($request->get('token'))) {
            $url = $this->generateUrl('sylius_user_request_password_reset_pin');
        }

        return new RedirectResponse($url);
    }

    // TODO will be replaced by denyAccessUnlessGranted after bump to Symfony 2.7
    public function validateAccess()
    {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException('You have to be registered user to access this section.');
        }
    }
}
