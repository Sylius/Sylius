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

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\UserBundle\Form\Model\ChangePassword;
use Sylius\Bundle\UserBundle\Form\Model\PasswordReset;
use Sylius\Bundle\UserBundle\Form\Model\PasswordResetRequest;
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
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException('You have to be registered user to access this section.');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $changePassword = new ChangePassword();
        $formType = $request->attributes->get('_sylius[form]', 'sylius_user_change_password', true);
        $form = $this->createResourceForm($configuration, $formType, $changePassword);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            return $this->handleChangePassword($request, $configuration, $user, $changePassword->getNewPassword());
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, 400));
        }

        return $this->container->get('templating')->renderResponse(
            $configuration->getTemplate('changePassword.html'),
            ['form' => $form->createView()]
        );
    }

    public function requestPasswordResetTokenAction(Request $request)
    {
        $generator = $this->container->get('sylius.user.token_provider');

        return $this->prepareResetPasswordRequest($request, $generator, UserEvents::REQUEST_RESET_PASSWORD_TOKEN);
    }

    public function requestPasswordResetPinAction(Request $request)
    {
        $generator = $this->container->get('sylius.user.pin_provider');

        return $this->prepareResetPasswordRequest($request, $generator, UserEvents::REQUEST_RESET_PASSWORD_PIN);
    }

    public function resetPasswordAction(Request $request, $token)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $user = $this->findUserByToken($token);

        $lifetime = new \DateInterval($this->container->getParameter('sylius.user.resetting.token_ttl'));
        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            return $this->handleExpiredToken($configuration, $token, $user);
        }

        $changePassword = new PasswordReset();
        $formType = $request->attributes->get('_sylius[form]', 'sylius_user_reset_password', true);
        $form = $this->createResourceForm($configuration, $formType, $changePassword);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            return $this->handleResetPassword($request, $configuration, $user, $changePassword->getPassword());
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, 400));
        }

        return $this->container->get('templating')->renderResponse(
            $configuration->getTemplate('resetPassword.html'),
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    protected function prepareResetPasswordRequest(Request $request, TokenProviderInterface $generator, $senderEvent)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $passwordReset = new PasswordResetRequest();
        $formType = $request->attributes->get('_sylius[form]', 'sylius_user_request_password_reset', true);
        $form = $this->createResourceForm($configuration, $formType, $passwordReset);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $user = $this->repository->findOneByEmail($passwordReset->getEmail());
            if (null !== $user) {
                $this->handleResetPasswordRequest($generator, $user, $senderEvent);
            }

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create($user, 204));
            }

            $this->addFlash('success', 'sylius.user.reset_password.requested');

            return new RedirectResponse($this->container->get('router')->generate('sylius_user_security_login'));
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, 400));
        }

        return $this->container->get('templating')->renderResponse(
            $configuration->getTemplate('requestPasswordReset.html'),
            [
                'form' => $form->createView(),
            ]
        );
    }

    protected function addFlash($type, $message)
    {
        $translator = $this->container->get('translator');
        $this->container->get('session')->getFlashBag()->add($type, $translator->trans($message, [], 'flashes'));
    }

    protected function createResourceForm(RequestConfiguration $configuration, $type, $resource)
    {
        if (!$configuration->isHtmlRequest()) {
            return $this->container->get('form.factory')->createNamed('', $type, $resource, ['csrf_protection' => false]);
        }

        return $this->container->get('form.factory')->create($type, $resource);
    }

    /**
     * @param RequestConfiguration $configuration
     * @param string $token
     * @param UserInterface $user
     *
     * @return RedirectResponse
     */
    protected function handleExpiredToken(RequestConfiguration $configuration, $token, UserInterface $user)
    {
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);

        $this->manager->flush();

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($user, 400));
        }

        $this->addFlash('error', 'sylius.user.password.token_expired');

        $url = $this->generateResetPasswordRequestUrl($token);

        return new RedirectResponse($url);
    }

    /**
     * @param TokenProviderInterface $generator
     * @param UserInterface $user
     * @param string $senderEvent
     *
     * @return Response
     */
    protected function handleResetPasswordRequest(TokenProviderInterface $generator, UserInterface $user, $senderEvent)
    {
        $user->setConfirmationToken($generator->generateUniqueToken());
        $user->setPasswordRequestedAt(new \DateTime());

        /* I have to use doctrine manager directly, because domain manager functions add a flash messages. I can't get rid of them.*/
        $manager = $this->container->get('doctrine.orm.default_entity_manager');
        $manager->persist($user);
        $manager->flush();

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($senderEvent, new GenericEvent($user));
    }

    /**
     * @param Request $request
     * @param RequestConfiguration $configuration
     * @param UserInterface $user
     * @param string $newPassword
     *
     * @return RedirectResponse
     */
    protected function handleResetPassword(Request $request, RequestConfiguration $configuration, UserInterface $user, $newPassword)
    {
        $user->setPlainPassword($newPassword);
        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(UserEvents::PRE_PASSWORD_RESET, new GenericEvent($user));

        $this->manager->flush();
        $this->addFlash('success', 'sylius.user.password.reset.success');

        $dispatcher->dispatch(UserEvents::POST_PASSWORD_RESET, new GenericEvent($user));

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($user, 204));
        }

        $redirectRouteName = $request->attributes->get('_sylius[redirect]', 'sylius_user_security_login', true);

        return new RedirectResponse($this->container->get('router')->generate($redirectRouteName));
    }

    /**
     * @param Request $request
     * @param RequestConfiguration $configuration
     * @param UserInterface $user
     * @param string $newPassword
     *
     * @return RedirectResponse
     */
    protected function handleChangePassword(Request $request, RequestConfiguration $configuration, UserInterface $user, $newPassword)
    {
        $user->setPlainPassword($newPassword);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(UserEvents::PRE_PASSWORD_CHANGE, new GenericEvent($user));

        $this->manager->flush();
        $this->addFlash('success', 'sylius.user.password.change.success');

        $dispatcher->dispatch(UserEvents::POST_PASSWORD_CHANGE, new GenericEvent($user));

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($user, 204));
        }

        $redirectRouteName = $request->attributes->get('_sylius[redirect]', 'sylius_account_profile_show', true);

        return new RedirectResponse($this->container->get('router')->generate($redirectRouteName));
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
        $user = $this->repository->findOneBy(['confirmationToken' => $token]);
        if (null === $user) {
            throw new NotFoundHttpException('This token does not exist');
        }

        return $user;
    }

    /**
     * @param string $token
     *
     * @return string
     */
    protected function generateResetPasswordRequestUrl($token)
    {
        $router = $this->container->get('router');

        if (is_numeric($token)) {
            return $router->generate('sylius_user_request_password_reset_pin');
        }

        return $router->generate('sylius_user_request_password_reset_token');
    }
}
