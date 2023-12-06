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

use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ChannelDefaultLocaleEnabledValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var ChannelDefaultLocaleEnabled $constraint */
        Assert::isInstanceOf($constraint, ChannelDefaultLocaleEnabled::class);

        /** @var ChannelInterface $value */
        Assert::isInstanceOf($value, ChannelInterface::class);

        $defaultLocale = $value->getDefaultLocale();
        if ($defaultLocale !== null && !$value->hasLocale($defaultLocale)) {
            $this->context->buildViolation($constraint->message)->atPath('defaultLocale')->addViolation();
        }
    }
}
