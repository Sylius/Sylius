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

namespace Sylius\Behat\Element\Admin\Locale;

use Sylius\Behat\Element\Admin\Crud\FormElementInterface as BaseFormElementInterface;
use Sylius\Component\Core\Model\TaxonInterface;

interface FormElementInterface extends BaseFormElementInterface
{
    public function chooseLocale(string $localeName): void;

    public function isLocaleAvailable(string $localeName): bool;
}
