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

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\UserBundle\Form\Model\ChangePassword;
use Sylius\Bundle\UserBundle\Form\Model\PasswordReset;
use Sylius\Bundle\UserBundle\Form\Model\PasswordResetRequest;
use Sylius\Bundle\UserBundle\Form\Type\UserChangePasswordType;
use Sylius\Bundle\UserBundle\Form\Type\UserRequestPasswordResetType;
use Sylius\Bundle\UserBundle\Form\Type\UserResetPasswordType;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Webmozart\Assert\Assert;

class UserController extends ResourceController
{
    public function changePasswordAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (!$this->container->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException('You have to be registered user to access this section.');
        }

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $changePassword = new ChangePassword();
        $formType = $this->getSyliusAttribute($request, 'form', UserChangePasswordType::class);
        $form = $this->createResourceForm($configuration, $formType, $changePassword);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isValid()) {
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

    public function requestPasswordResetTokenAction(Request $request): Response
    {
        /** @var GeneratorInterface $generator */
        $generator = $this->container->get(sprintf('sylius.%s.token_generator.password_reset', $this->metadata->getName()));

        return $this->prepareResetPasswordRequest($request, $generator, UserEvents::REQUEST_RESET_PASSWORD_TOKEN);
    }

    public function requestPasswordResetPinAction(Request $request): Response
    {
        /** @var GeneratorInterface $generator */
        $generator = $this->container->get(sprintf('sylius.%s.pin_generator.password_reset', $this->metadata->getName()));

        return $this->prepareResetPasswordRequest($request, $generator, UserEvents::REQUEST_RESET_PASSWORD_PIN);
    }

    public function resetPasswordAction(Request $request, string $token): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        /** @var UserInterface|null $user */
        $user = $this->repository->findOneBy(['passwordResetToken' => $token]);
        if (null === $user) {
            throw new NotFoundHttpException('Token not found.');
        }

        $resetting = $this->metadata->getParameter('resetting');
        $lifetime = new \DateInterval($resetting['token']['ttl']);
        if (!$user->isPasswordRequestNonExpired($lifetime)) {
            return $this->handleExpiredToken($request, $configuration, $user);
        }

        $passwordReset = new PasswordReset();
        $formType = $this->getSyliusAttribute($request, 'form', UserResetPasswordType::class);
        $form = $this->createResourceForm($configuration, $formType, $passwordReset);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isValid()) {
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

    public function verifyAction(Request $request, string $token): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $redirectRoute = $this->getSyliusAttribute($request, 'redirect', null);

        $response = $this->redirectToRoute($redirectRoute);

        /** @var UserInterface|null $user */
        $user = $this->repository->findOneBy(['emailVerificationToken' => $token]);
        if (null === $user) {
            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create($configuration, Response::HTTP_BAD_REQUEST));
            }

            $this->addTranslatedFlash('error', 'sylius.user.verify_email_by_invalid_token');

            return $this->redirectToRoute($redirectRoute);
        }

        $user->setVerifiedAt(new \DateTime());
        $user->setEmailVerificationToken(null);
        $user->enable();

        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(UserEvents::PRE_EMAIL_VERIFICATION, new GenericEvent($user));

        $this->manager->flush();

        $eventDispatcher->dispatch(UserEvents::POST_EMAIL_VERIFICATION, new GenericEvent($user));

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($user));
        }

        $flashMessage = $this->getSyliusAttribute($request, 'flash', 'sylius.user.verify_email');
        $this->addTranslatedFlash('success', $flashMessage);

        return $response;
    }

    public function requestVerificationTokenAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $redirectRoute = $this->getSyliusAttribute($request, 'redirect', 'referer');

        $user = $this->getUser();
        if (null === $user) {
            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create($configuration, Response::HTTP_UNAUTHORIZED));
            }

            $this->addTranslatedFlash('notice', 'sylius.user.verify_no_user');

            return $this->redirectHandler->redirectToRoute($configuration, $redirectRoute);
        }

        if (null !== $user->getVerifiedAt()) {
            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create($configuration, Response::HTTP_BAD_REQUEST));
            }

            $this->addTranslatedFlash('notice', 'sylius.user.verify_verified_email');

            return $this->redirectHandler->redirectToRoute($configuration, $redirectRoute);
        }

        /** @var GeneratorInterface $tokenGenerator */
        $tokenGenerator = $this->container->get(sprintf('sylius.%s.token_generator.email_verification', $this->metadata->getName()));
        $user->setEmailVerificationToken($tokenGenerator->generate());

        $this->manager->flush();

        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(UserEvents::REQUEST_VERIFICATION_TOKEN, new GenericEvent($user));

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        $this->addTranslatedFlash('success', 'sylius.user.verify_email_request');

        return $this->redirectHandler->redirectToRoute($configuration, $redirectRoute);
    }

    protected function prepareResetPasswordRequest(Request $request, GeneratorInterface $generator, string $senderEvent): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $passwordReset = new PasswordResetRequest();
        $formType = $this->getSyliusAttribute($request, 'form', UserRequestPasswordResetType::class);
        $form = $this->createResourceForm($configuration, $formType, $passwordReset);
        $template = $this->getSyliusAttribute($request, 'template', null);
        if ($configuration->isHtmlRequest()) {
            Assert::notNull($template, 'Template is not configured.');
        }

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isValid()) {
            $userRepository = $this->repository;

            /** @var UserRepositoryInterface $userRepository */
            Assert::isInstanceOf($userRepository, UserRepositoryInterface::class);

            $user = $userRepository->findOneByEmail($passwordReset->getEmail());
            if (null !== $user) {
                $this->handleResetPasswordRequest($generator, $user, $senderEvent);
            }

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
            }

            $this->addTranslatedFlash('success', 'sylius.user.reset_password_request');
            $redirectRoute = $this->getSyliusAttribute($request, 'redirect', null);
            Assert::notNull($redirectRoute, 'Redirect is not configured.');

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
     * {@inheritdoc}
     */
    protected function addTranslatedFlash(string $type, string $message): void
    {
        $translator = $this->container->get('translator');
        $this->container->get('session')->getFlashBag()->add($type, $translator->trans($message, [], 'flashes'));
    }

    /**
     * @param object $object
     */
    protected function createResourceForm(
        RequestConfiguration $configuration,
        string $type,
        $object
    ): FormInterface {
        if (!$configuration->isHtmlRequest()) {
            return $this->container->get('form.factory')->createNamed('', $type, $object, ['csrf_protection' => false]);
        }

        return $this->container->get('form.factory')->create($type, $object);
    }

    protected function handleExpiredToken(Request $request, RequestConfiguration $configuration, UserInterface $user): Response
    {
        $user->setPasswordResetToken(null);
        $user->setPasswordRequestedAt(null);

        $this->manager->flush();

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($user, Response::HTTP_BAD_REQUEST));
        }

        $this->addTranslatedFlash('error', 'sylius.user.expire_password_reset_token');

        $redirectRouteName = $this->getSyliusAttribute($request, 'redirect', null);
        Assert::notNull($redirectRouteName, 'Redirect is not configured.');

        return new RedirectResponse($this->container->get('router')->generate($redirectRouteName));
    }

    protected function handleResetPasswordRequest(
        GeneratorInterface $generator,
        UserInterface $user,
        string $senderEvent
    ): void {
        $user->setPasswordResetToken($generator->generate());
        $user->setPasswordRequestedAt(new \DateTime());

        // I have to use doctrine manager directly, because domain manager functions add a flash messages. I can't get rid of them.
        $manager = $this->container->get('doctrine.orm.default_entity_manager');
        $manager->persist($user);
        $manager->flush();

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch($senderEvent, new GenericEvent($user));
    }

    protected function handleResetPassword(
        Request $request,
        RequestConfiguration $configuration,
        UserInterface $user,
        string $newPassword
    ): Response {
        $user->setPlainPassword($newPassword);
        $user->setPasswordResetToken(null);
        $user->setPasswordRequestedAt(null);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(UserEvents::PRE_PASSWORD_RESET, new GenericEvent($user));

        $this->manager->flush();
        $this->addTranslatedFlash('success', 'sylius.user.reset_password');

        $dispatcher->dispatch(UserEvents::POST_PASSWORD_RESET, new GenericEvent($user));

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        $redirectRouteName = $this->getSyliusAttribute($request, 'redirect', null);
        Assert::notNull($redirectRouteName, 'Redirect is not configured.');

        return new RedirectResponse($this->container->get('router')->generate($redirectRouteName));
    }

    protected function handleChangePassword(
        Request $request,
        RequestConfiguration $configuration,
        UserInterface $user,
        string $newPassword
    ): Response {
        $user->setPlainPassword($newPassword);

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(UserEvents::PRE_PASSWORD_CHANGE, new GenericEvent($user));

        $this->manager->flush();
        $this->addTranslatedFlash('success', 'sylius.user.change_password');

        $dispatcher->dispatch(UserEvents::POST_PASSWORD_CHANGE, new GenericEvent($user));

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        $redirectRouteName = $this->getSyliusAttribute($request, 'redirect', null);
        Assert::notNull($redirectRouteName, 'Redirect is not configured.');

        return new RedirectResponse($this->container->get('router')->generate($redirectRouteName));
    }

    protected function getUser(): ?UserInterface
    {
        $user = parent::getUser();
        $authorizationChecker = $this->container->get('security.authorization_checker');

        if (
            $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED') &&
            $user instanceof UserInterface
        ) {
            return $user;
        }

        return null;
    }

    private function getSyliusAttribute(Request $request, string $attribute, $default = null)
    {
        $attributes = $request->attributes->get('_sylius');

        return $attributes[$attribute] ?? $default;
    }
}
