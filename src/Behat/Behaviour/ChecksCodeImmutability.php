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

namespace Sylius\Behat\Behaviour;

use Behat\Mink\Element\NodeElement;

trait ChecksCodeImmutability
{
    abstract protected function getCodeElement(): NodeElement;

    public function isCodeDisabled(): bool
    {
        return 'disabled' === $this->getCodeElement()->getAttribute('disabled');
    }
}
