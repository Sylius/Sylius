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

namespace Sylius\Bundle\LocaleBundle\Validator;

use Symfony\Component\Validator\Constraint;

final class TranslationForExistingLocales extends Constraint
{
    public string $message = 'sylius.translation.locale.not_available';

    public function validatedBy(): string
    {
        return 'sylius_translation_for_existing_locales';
    }

    public function getTargets(): string
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}
