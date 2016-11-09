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
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class UserController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function changePasswordAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (!$this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException('You have to be registered user to access this section.');
        }

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $changePassword = new ChangePassword();
        $formType = $request->attributes->get('_sylius[form]', 'sylius_user_change_password', true);
        $form = $this->createResourceForm($configuration, $formType, $changePassword);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            return $this->handleChangePassword($request, $configuration, $user, $changePassword->getNewPassword());
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        return $this->container->get('templating')->renderResponse(
            $configuration->getTemplate('changePassword.html'),
            ['form' => $form->createView()]
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function requestPasswordResetTokenAction(Request $request)
    {
        /** @var GeneratorInterface $generator */
        $generator = $this->container->get(sprintf('sylius.%s.token_generator.password_reset', $this->metadata->getName()));

        return $this->prepareResetPasswordRequest($request, $generator, UserEvents::REQUEST_RESET_PASSWORD_TOKEN);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function requestPasswordResetPinAction(Request $request)
    {
        /** @var GeneratorInterface $generator */
        $generator = $this->container->get(sprintf('sylius.%s.pin_generator.password_reset', $this->metadata->getName()));

        return $this->prepareResetPasswordRequest($request, $generator, UserEvents::REQUEST_RESET_PASSWORD_PIN);
    }

    /**
     * @param Request $request
     * @param string $token
     *
     * @return Response
     */
    public function resetPasswordAction(Request $request, $token)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        /** @var UserInterface $user */
        $user = $this->repository->findOneBy(['passwordResetToken' => $token]);
        if (null === $user) {
            throw new NotFoundHttpException('Token not found.');
        }

        $resetting = $this->metadata->getParameter('resetting');
        $lifetime = new \DateInterval($resetting['token']['ttl']);
        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            return $this->handleExpiredToken($configuration, $token, $user);
        }

        $passwordReset = new PasswordReset();
        $formType = $request->attributes->get('_sylius[form]', 'sylius_user_reset_password', true);
        $form = $this->createResourceForm($configuration, $formType, $passwordReset);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            return $this->handleResetPassword($request, $configuration, $user, $passwordReset->getPassword());
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        return $this->container->get('templating')->renderResponse(
            $configuration->getTemplate('resetPassword.html'),
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * @param Request $request
     * @param string $token
     *
     * @return Response
     */
    public function verifyAction(Request $request, $token)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $redirectRoute = $request->attributes->get('_sylius[redirect]', null, true);

        $response = $this->redirectToRoute($redirectRoute);

        /** @var UserInterface $user */
        $user = $this->repository->findOneBy(['emailVerificationToken' => $token]);
        if (null === $user) {
            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create($configuration, Response::HTTP_BAD_REQUEST));
            }

            $this->addFlash('error', 'sylius.user.verification.error');

            return $this->redirectToRoute($redirectRoute);
        }

        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(UserEvents::PRE_EMAIL_VERIFICATION, new GenericEvent($user));

        $user->setVerifiedAt(new \DateTime());
        $user->setEmailVerificationToken(null);

        $this->manager->flush();

        $eventDispatcher->dispatch(UserEvents::POST_EMAIL_VERIFICATION, new GenericEvent($user));

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($user));
        }

        $flashMessage = $request->attributes->get('_sylius[flash]', 'sylius.user.verification.success');
        $this->addFlash('success', $flashMessage);

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function requestVerificationTokenAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $redirectRoute = $request->attributes->get('_sylius[redirect]', 'referer', true);

        /** @var UserInterface $user */
        $user = $this->container->get('sylius.context.customer')->getCustomer()->getUser();
        if (null !== $user->getVerifiedAt()) {
            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create($configuration, Response::HTTP_BAD_REQUEST));
            }

            $this->addFlash('notice', 'sylius.user.verification.notice.verified');

            return $this->redirectHandler->redirectToRoute($configuration, $redirectRoute);
        }

        $tokenGenerator = $this->container->get(sprintf('sylius.%s.token_generator.email_verification', $this->metadata->getName()));
        $user->setEmailVerificationToken($tokenGenerator->generate());

        $this->manager->flush();

        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(UserEvents::REQUEST_VERIFICATION_TOKEN, new GenericEvent($user));

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        $this->addFlash('success', 'sylius.user.verification.request.success');

        return $this->redirectHandler->redirectToRoute($configuration, $redirectRoute);
    }

    /**
     * @param Request $request
     * @param GeneratorInterface $generator
     * @param string $senderEvent
     *
     * @return Response
     */
    protected function prepareResetPasswordRequest(Request $request, GeneratorInterface $generator, $senderEvent)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $passwordReset = new PasswordResetRequest();
        $formType = $request->attributes->get('_sylius[form]', 'sylius_user_request_password_reset', true);
        $form = $this->createResourceForm($configuration, $formType, $passwordReset);
        $template = $request->attributes->get('_sylius[template]', 'SyliusUserBundle:User:requestPasswordReset.html.twig', true);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $user = $this->repository->findOneByEmail($passwordReset->getEmail());
            if (null !== $user) {
                $this->handleResetPasswordRequest($generator, $user, $senderEvent);
            }

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
            }

            $this->addFlash('success', 'sylius.user.password.reset.requested');
            $redirectRoute = $request->attributes->get('_sylius[redirect]', 'sylius_user_security_login', true);

            if (is_array($redirectRoute)) {
                return $this->redirectHandler->redirectToRoute(
                    $configuration,
                    $configuration->getParameters()->get('redirect')['route'],
                    $configuration->getParameters()->get('redirect')['parameters']
                );
            }

            return $this->redirectHandler->redirectToRoute($configuration, $redirectRoute);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        return $this->container->get('templating')->renderResponse(
            $template,
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param string $type
     * @param string $message
     */
    protected function addFlash($type, $message)
    {
        $translator = $this->container->get('translator');
        $this->container->get('session')->getFlashBag()->add($type, $translator->trans($message, [], 'flashes'));
    }

    /**
     * @param RequestConfiguration $configuration
     * @param string $type
     * @param mixed $resource
     *
     * @return FormInterface
     */
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
        $user->setPasswordResetToken(null);
        $user->setPasswordRequestedAt(null);

        $this->manager->flush();

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($user, Response::HTTP_BAD_REQUEST));
        }

        $this->addFlash('error', 'sylius.user.password.reset.token_expired');

        $url = $this->generateRequestPasswordResetUrl($token);

        return new RedirectResponse($url);
    }

    /**
     * @param GeneratorInterface $generator
     * @param UserInterface $user
     * @param string $senderEvent
     */
    protected function handleResetPasswordRequest(GeneratorInterface $generator, UserInterface $user, $senderEvent)
    {
        $user->setPasswordResetToken($generator->generate());
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
     * @return Response
     */
    protected function handleResetPassword(Request $request, RequestConfiguration $configuration, UserInterface $user, $newPassword)
    {
        $user->setPlainPassword($newPassword);
        $user->setPasswordResetToken(null);
        $user->setPasswordRequestedAt(null);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(UserEvents::PRE_PASSWORD_RESET, new GenericEvent($user));

        $this->manager->flush();
        $this->addFlash('success', 'sylius.user.password.reset.success');

        $dispatcher->dispatch(UserEvents::POST_PASSWORD_RESET, new GenericEvent($user));

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
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
     * @return Response
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
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        $redirectRouteName = $request->attributes->get('_sylius[redirect]', 'sylius_account_profile_show', true);

        return new RedirectResponse($this->container->get('router')->generate($redirectRouteName));
    }

    /**
     * @param string $token
     *
     * @return string
     */
    protected function generateRequestPasswordResetUrl($token)
    {
        $router = $this->container->get('router');

        if (is_numeric($token)) {
            return $router->generate('sylius_user_request_password_reset_pin');
        }

        return $router->generate('sylius_user_request_password_reset_token');
    }
}
