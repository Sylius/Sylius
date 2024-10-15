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

trait NavigationTrait
{
    public function hasShowPageButton(): bool
    {
        return $this->hasElement('show_' . $this->getResourceName() . '_button');
    }

    public function switchToShowPage(): void
    {
        $this->getElement('show_' . $this->getResourceName() . '_button')->click();
    }

    public function hasEditPageButton(): bool
    {
        return $this->hasElement('edit_' . $this->getResourceName() . '_button');
    }

    public function switchToEditPage(): void
    {
        $this->getElement('edit_' . $this->getResourceName() . '_button')->click();
    }

    abstract protected function getResourceName(): string;
}
