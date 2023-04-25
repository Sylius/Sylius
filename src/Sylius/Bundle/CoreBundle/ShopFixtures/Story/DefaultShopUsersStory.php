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

namespace Sylius\Bundle\CoreBundle\ShopFixtures\Story;

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneShopUser;
use Sylius\Bundle\CoreBundle\ShopFixtures\Symfony\Messenger\CommandBusInterface;

final class DefaultShopUsersStory implements DefaultShopUsersStoryInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function create(): void
    {
        $this->commandBus->dispatch(
            (new CreateOneShopUser())
                ->withEmail('test@example.com')
                ->withFirstName('John')
                ->withLastName('Doe')
                ->withPassword('sylius')
        );
    }
}
