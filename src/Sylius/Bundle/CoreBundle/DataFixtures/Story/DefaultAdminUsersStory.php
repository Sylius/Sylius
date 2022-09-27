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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\AdminUserFactoryInterface;
use Zenstruck\Foundry\Story;

final class DefaultAdminUsersStory extends Story implements DefaultAdminUsersStoryInterface
{
    public function __construct(
        private AdminUserFactoryInterface $adminUserFactory,
        private string $baseLocaleCode,
    ) {
    }

    public function build(): void
    {
        $this->adminUserFactory::new()
            ->withEmail('sylius@example.com')
            ->withUsername('sylius')
            ->withPassword('sylius')
            ->enabled()
            ->withLocaleCode($this->baseLocaleCode)
            ->withFirstName('John')
            ->withLastName('Doe')
            ->withAvatar('@SyliusCoreBundle/Resources/fixtures/adminAvatars/john.jpg')
            ->create()
        ;

        $this->adminUserFactory::new()
            ->withEmail('api@example.com')
            ->withUsername('api')
            ->withPassword('sylius')
            ->enabled()
            ->withLocaleCode($this->baseLocaleCode)
            ->withFirstName('Luke')
            ->withLastName('Brushwood')
            ->withAvatar('@SyliusCoreBundle/Resources/fixtures/adminAvatars/luke.jpg')
            ->create()
        ;
    }
}
