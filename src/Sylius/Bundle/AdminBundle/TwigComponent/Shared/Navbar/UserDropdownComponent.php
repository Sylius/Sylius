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

namespace Sylius\Bundle\AdminBundle\TwigComponent\Shared\Navbar;

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

final class UserDropdownComponent
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $translator,
        private Security $security,
    ) {
    }

    #[ExposeInTemplate]
    public function getUser(): UserInterface
    {
        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            throw new \RuntimeException('User must be an instance of Sylius\Component\User\Model\UserInterface');
        }

        return $user;
    }

    /**
     * @return array<string, array<string, array{title?: string, url?: string, icon?: string, type?: string, class?: string}>>
     *
     * @psalm-suppress InvalidReturnType
     */
    #[ExposeInTemplate]
    public function getMenuItems(): array
    {
        /**
         * @phpstan-ignore-next-line PHPStan complains the declared return type does not match the returned value
         *
         * @psalm-suppress InvalidReturnStatement
         */
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
}
