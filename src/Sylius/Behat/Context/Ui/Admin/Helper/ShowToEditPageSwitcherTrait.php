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

namespace Sylius\Behat\Context\Ui\Admin\Helper;

trait ShowToEditPageSwitcherTrait
{
    private string $resourceName;

    public function hasEditPageButton(): bool
    {
        return $this->hasElement('edit_' . $this->resourceName . '_button');
    }

    public function switchToEditPage(): void
    {
        $this->getElement('edit_' . $this->resourceName . '_button')->click();
    }

    abstract protected function defineResourceName(): void;
}
