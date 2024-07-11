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

trait ShowPageButtonCheckerTrait
{
    private string $resourceName;

    public function hasShowPageButton(): bool
    {
        return $this->hasElement('show_' . $this->resourceName . '_button');
    }

    abstract protected function defineResourceName(): void;
}
