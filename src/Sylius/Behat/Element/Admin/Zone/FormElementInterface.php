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

namespace Sylius\Behat\Element\Admin\Zone;

use Sylius\Behat\Element\Admin\Crud\FormElementInterface as BaseFormElementInterface;

interface FormElementInterface extends BaseFormElementInterface
{
    public function getName(): string;

    public function nameIt(string $name): void;

    public function getType(): string;

    public function isTypeFieldDisabled(): bool;

    public function isCodeDisabled(): bool;

    public function specifyCode(string $code): void;

    public function addMember(): void;

    public function getScope(): string;

    public function selectScope(string $scope): void;

    public function hasMember(string $member): bool;

    public function countMembers(): int;

    public function removeMember(string $member): void;

    public function chooseMember(string $name): void;

    public function getFormValidationMessage(): string;
}
