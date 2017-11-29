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
    public function addMember(): void;

    /**
     * @param string $message
     */
    public function checkValidationMessageForMembers(string $message): void;

    /**
     * @param string $name
     */
    public function chooseMember(string $name): void;

    /**
     * @param string $scope
     */
    public function selectScope(string $scope): void;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasType(string $type): bool;

    /**
     * @return bool
     */
    public function isTypeFieldDisabled(): bool;

    /**
     * @param string $name
     */
    public function nameIt(string $name): void;

    /**
     * @param string $code
     */
    public function specifyCode(string $code): void;
}
