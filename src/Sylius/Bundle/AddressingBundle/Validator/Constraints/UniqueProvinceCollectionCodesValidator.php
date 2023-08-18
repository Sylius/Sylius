<?php

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Validator\Constraints;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class UniqueProvinceCollectionCodesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /** @var Collection<array-key, ProvinceInterface> $value */
        Assert::allIsInstanceOf($value, ProvinceInterface::class);
        /** @var UniqueProvinceCollectionCodes $constraint */
        Assert::isInstanceOf($constraint, UniqueProvinceCollectionCodes::class);

        if ($value->isEmpty()) {
            return;
        }

        $codes = $value
            ->filter(fn (ProvinceInterface $province): bool => null !== $province->getCode())
            ->map(fn (ProvinceInterface $province): string => $province->getCode())
            ->toArray()
        ;

        $uniqueCodes = array_unique($codes);

        if (count($codes) !== count($uniqueCodes)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
