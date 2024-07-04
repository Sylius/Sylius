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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Shared\Navbar;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

class UserDropdownComponent
{
    /** @param UserRepositoryInterface<AdminUserInterface> $adminUserRepository */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $translator,
        private Security $security,
        private RequestStack $requestStack,
        private UserRepositoryInterface $adminUserRepository,
    ) {
    }

    #[ExposeInTemplate(name: 'user')]
    public function getUser(): AdminUserInterface
    {
        $user = $this->security->getUser();
        if (null === $user) {
            $user = $this->requestStack->getMainRequest()->getUser();
        }
        if (null === $user) {
            $user = $this->getUserFromSession();
        }

        if (!$user instanceof AdminUserInterface) {
            throw new \RuntimeException('User must be an instance of Sylius\Component\User\Model\UserInterface');
        }

        return $user;
    }

    /**
     * @return array<array-key, array<array-key, array{title?: string, url?: string, icon?: string, type?: string, class?: string, attr?: array<string, mixed>}>>
     */
    #[ExposeInTemplate(name: 'menu_items')]
    public function getMenuItems(): array
    {
        // TODO: Would be nice to have these set via hook //
        return [
            [
                'title' => $this->translator->trans('sylius.ui.my_account'),
                'url' => $this->urlGenerator->generate('sylius_admin_admin_user_update', ['id' => $this->getUser()->getId()]),
                'icon' => 'user',
            ],
            [
                'title' => $this->translator->trans('sylius.ui.logout'),
                'url' => $this->urlGenerator->generate('sylius_admin_logout'),
                'icon' => 'logout',
                'attr' => [
                    'data-test-logout' => null,
                ],
            ],
            [
                'type' => 'divider',
            ],
            [
                'title' => $this->translator->trans('sylius.ui.documentation'),
                'url' => 'https://docs.sylius.com',
                'class' => 'small text-muted',
            ],
            [
                'title' => $this->translator->trans('sylius.ui.join_slack'),
                'url' => 'https://sylius.com/slack',
                'class' => 'small text-muted',
            ],
            [
                'title' => $this->translator->trans('sylius.ui.report_an_issue'),
                'url' => 'https://github.com/Sylius/Sylius/issues',
                'class' => 'small text-muted',
            ],
        ];
    }

    public function getUserFromSession(): ?AdminUserInterface
    {
        $serializedToken = $this->requestStack->getSession()->get('_security_admin');
        if (null === $serializedToken) {
            return null;
        }

        /** @var false|TokenInterface $token */
        $token = unserialize($serializedToken);
        if (false === $token) {
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof AdminUserInterface) {
            return null;
        }

        return $this->adminUserRepository->find($user->getId());
    }
}
