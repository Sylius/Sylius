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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class AllowedImageMimeTypesValidator extends ConstraintValidator
{
    /**
     * @param array<string> $allowedMimeTypes
     */
    public function __construct(private readonly array $allowedMimeTypes)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        Assert::isInstanceOf($value, File::class);
        Assert::isInstanceOf($constraint, AllowedImageMimeTypes::class);

        if (!in_array($value->getMimeType(), $this->allowedMimeTypes, true)) {
            $this->context->buildViolation($constraint->message, ['%types%' => implode(', ', $this->allowedMimeTypes)])->addViolation();
        }
    }
}
