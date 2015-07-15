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
use Symfony\Component\HttpFoundation\Response;
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
                return $this->handleChangePassword($user, $changePassword->getNewPassword());
            }

            $this->addFlash('error', 'sylius.user.password.invalid');
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

    public function resetPasswordAction(Request $request, $token)
    {
        $user = $this->findUserByToken($token);

        $lifetime = new \DateInterval($this->container->getParameter('sylius.user.resetting.token_ttl'));
        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            return $this->handleExpiredToken($token, $user);
        }

        $changePassword = new ChangePassword();
        $form = $this->createResourceForm(new UserResetPasswordType(), $changePassword);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            return $this->handleResetPassword($user, $changePassword->getNewPassword());
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
                return $this->handleResetPasswordRequest($generator, $user, $senderEvent);
            }

            $this->addFlash('error', 'sylius.user.email.not_exist');
            $this->addFlash('error', 'sylius.user.password.reset.failed');
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
     * @param string        $token
     * @param UserInterface $user
     *
     * @return RedirectResponse
     */
    protected function handleExpiredToken($token, UserInterface $user)
    {
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);

        $this->domainManager->update($user);

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($user, 400));
        }

        $this->addFlash('error', 'sylius.user.password.token_expired');

        $url = $this->generateResetPasswordRequestUrl($token);

        return new RedirectResponse($url);
    }

    /**
     * @param TokenProviderInterface $generator
     * @param UserInterface          $user
     * @param string                 $senderEvent
     *
     * @return Response
     */
    protected function handleResetPasswordRequest(TokenProviderInterface $generator, UserInterface $user, $senderEvent)
    {
        $user->setConfirmationToken($generator->generateUniqueToken());
        $user->setPasswordRequestedAt(new \DateTime());

        $this->domainManager->update($user);

        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch($senderEvent, new GenericEvent($user));

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($user, 204));
        }

        $this->addFlash('success', 'sylius.user.password.reset.success');

        return new RedirectResponse($this->generateUrl('sylius_homepage'));
    }

    /**
     * @param UserInterface $user
     * @param string        $newPassword
     *
     * @return RedirectResponse
     */
    protected function handleResetPassword(UserInterface $user, $newPassword)
    {
        $user->setPlainPassword($newPassword);
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);

        $this->domainManager->update($user);

        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(UserEvents::PASSWORD_RESET_SUCCESS, new GenericEvent($user));

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($user, 204));
        }
        $this->addFlash('success', 'sylius.user.password.change.success');

        return new RedirectResponse($this->generateUrl('sylius_user_security_login'));
    }

    /**
     * @param UserInterface $user
     * @param string        $newPassword
     *
     * @return RedirectResponse
     */
    protected function handleChangePassword(UserInterface $user, $newPassword)
    {
        $user->setPlainPassword($newPassword);

        $this->domainManager->update($user);

        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(UserEvents::PASSWORD_RESET_SUCCESS, new GenericEvent($user));

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($user, 204));
        }
        $this->addFlash('success', 'sylius.user.password.change.success');

        return new RedirectResponse($this->generateUrl('sylius_account_homepage'));
    }

    // TODO will be replaced by denyAccessUnlessGranted after bump to Symfony 2.7
    protected function validateAccess()
    {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException('You have to be registered user to access this section.');
        }
    }

    /**
     * @param string $token
     *
     * @throws NotFoundHttpException
     *
     * @return UserInterface
     */
    protected function findUserByToken($token)
    {
        $user = $this->getRepository()->findOneBy(array('confirmationToken' => $token));
        if (null === $user) {
            throw new NotFoundHttpException('This token does not exist');
        }

        return $user;
    }

    /**
     * @param $token
     *
     * @return string
     */
    protected function generateResetPasswordRequestUrl($token)
    {
        if (is_numeric($token)) {
            return $this->generateUrl('sylius_user_request_password_reset_pin');
        }

        return $this->generateUrl('sylius_user_request_password_reset_token');
    }
}
