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

namespace Sylius\Bundle\AddressingBundle\Validator\Constraints;

use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ZoneCannotContainItselfValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        /** @var ZoneCannotContainItself $constraint */
        Assert::isInstanceOf($constraint, ZoneCannotContainItself::class);

        /** @var ZoneMemberInterface $zoneMember */
        foreach ($value as $zoneMember) {
            if ($zoneMember->getCode() === $zoneMember->getBelongsTo()->getCode()) {
                $this->context->addViolation($constraint->message);
            }
        }
    }
}
