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

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ZoneCodeExistsValidator extends ConstraintValidator
{
    /** @param RepositoryInterface<ZoneInterface> $zoneRepository */
    public function __construct(private RepositoryInterface $zoneRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ZoneCodeExists) {
            throw new UnexpectedTypeException($constraint, ZoneCodeExists::class);
        }

        if (empty($value)) {
            return;
        }

        if ($this->zoneRepository->findOneBy(['code' => $value]) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->addViolation()
            ;
        }
    }
}
