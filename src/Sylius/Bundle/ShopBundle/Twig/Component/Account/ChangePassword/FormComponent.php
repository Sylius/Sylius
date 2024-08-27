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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Account\ChangePassword;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\UserBundle\Form\Model\ChangePassword;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class FormComponent
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp]
    public ?ChangePassword $changePassword = null;

    /** @param class-string $formClass */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly RouterInterface $router,
        private readonly ObjectManager $manager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
        private readonly string $formClass,
        private readonly string $redirectRouteName,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, $this->changePassword);
    }

    #[LiveAction]
    public function save(): RedirectResponse
    {
        $this->submitForm();

        /** @var UserInterface $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $request = $this->requestStack->getCurrentRequest();

        $user->setPlainPassword($this->formValues['newPassword']['first']);

        $this->eventDispatcher->dispatch(new GenericEvent($user), UserEvents::PRE_PASSWORD_CHANGE);

        $this->manager->flush();
        $this->addTranslatedFlash($request, 'success', 'sylius.user.change_password');

        $this->eventDispatcher->dispatch(new GenericEvent($user), UserEvents::POST_PASSWORD_CHANGE);

        return new RedirectResponse($this->router->generate($this->redirectRouteName));
    }

    private function addTranslatedFlash(Request $request, string $type, string $message): void
    {
        /** @var Session $session */
        $session = $request->getSession();

        $session->getFlashBag()->add($type, $this->translator->trans($message, [], 'flashes'));
    }
}
