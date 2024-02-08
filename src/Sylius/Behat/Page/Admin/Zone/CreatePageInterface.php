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

namespace Sylius\Behat\Page\Admin\Zone;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function addMember(): void;

    public function checkValidationMessageForMembers(string $message): bool;

    public function chooseMember(string $name): void;

    public function selectScope(string $scope): void;

    public function hasType(string $type): bool;

    public function isTypeFieldDisabled(): bool;

    public function nameIt(string $name): void;

    public function specifyCode(string $code): void;
}
