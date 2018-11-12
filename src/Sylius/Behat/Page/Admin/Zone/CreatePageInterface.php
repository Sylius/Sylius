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

namespace Sylius\Behat\Page\Admin\Zone;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function addMember();

    public function checkValidationMessageForMembers(string $message);

    public function chooseMember(string $name);

    public function selectScope(string $scope);

    public function hasType(string $type): bool;

    public function isTypeFieldDisabled(): bool;

    public function nameIt(string $name);

    public function specifyCode(string $code);
}
