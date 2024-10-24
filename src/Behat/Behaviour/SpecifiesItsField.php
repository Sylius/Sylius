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

trait SpecifiesItsField
{
    use DocumentAccessor;

    public function specifyCode(string $code): void
    {
        $this->getDocument()->fillField('Code', $code);
    }

    public function specifyField(string $field, string $value): void
    {
        $this->getDocument()->fillField($field, $value);
    }
}
