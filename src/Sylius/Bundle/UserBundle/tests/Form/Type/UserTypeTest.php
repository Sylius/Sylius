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

namespace Tests\Sylius\Bundle\UserBundle\Form\Type;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UserBundle\Form\Type\UserType;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserTypeTest extends TestCase
{
    public function testValidationGroupsClosureDoesNotMutateValidationGroupsAcrossCalls(): void
    {
        $userType = new class(User::class, ['sylius']) extends UserType {
        };

        $resolver = new OptionsResolver();
        $userType->configureOptions($resolver);
        $options = $resolver->resolve();

        /** @var callable(FormInterface): array<string> $validationGroups */
        $validationGroups = $options['validation_groups'];

        $newUser = $this->createMock(UserInterface::class);
        $newUser->method('getId')->willReturn(null);
        $newUserForm = $this->createMock(FormInterface::class);
        $newUserForm->method('getData')->willReturn($newUser);

        $existingUser = $this->createMock(UserInterface::class);
        $existingUser->method('getId')->willReturn(42);
        $existingUserForm = $this->createMock(FormInterface::class);
        $existingUserForm->method('getData')->willReturn($existingUser);

        $this->assertSame(['sylius', 'sylius_user_create'], $validationGroups($newUserForm));
        $this->assertSame(['sylius', 'sylius_user_create'], $validationGroups($newUserForm));
        $this->assertSame(['sylius'], $validationGroups($existingUserForm));
    }
}
