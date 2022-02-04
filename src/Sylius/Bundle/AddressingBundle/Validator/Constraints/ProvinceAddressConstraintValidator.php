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

use Sylius\Bundle\AddressingBundle\Checker\ProvinceAddressChecker;
use Sylius\Bundle\AddressingBundle\Checker\ProvinceAddressCheckerInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class ProvinceAddressConstraintValidator extends ConstraintValidator
{
    private ProvinceAddressCheckerInterface $provinceAddressChecker;

    public function __construct(object $countryRepositoryOrProvinceAddressChecker, ?RepositoryInterface $provinceRepository= null)
    {
        if (!$countryRepositoryOrProvinceAddressChecker instanceof ProvinceAddressCheckerInterface) {
            Assert::implementsInterface($countryRepositoryOrProvinceAddressChecker, RepositoryInterface::class);
            Assert::notNull($provinceRepository);

            @trigger_error(
                sprintf(
                    'Passing a $countryRepository and $provinceRepository to %s constructor is deprecated since Sylius 1.10 and will be removed in Sylius 2.0. Please, provide %s as first argument',
                    self::class,
                    ProvinceAddressCheckerInterface::class
                ),
                \E_USER_DEPRECATED
            );


            $this->provinceAddressChecker = new ProvinceAddressChecker($countryRepositoryOrProvinceAddressChecker, $provinceRepository);
        } else {
            $this->provinceAddressChecker = $countryRepositoryOrProvinceAddressChecker;
        }
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof AddressInterface) {
            throw new \InvalidArgumentException(
                'ProvinceAddressConstraintValidator can only validate instances of "Sylius\Component\Addressing\Model\AddressInterface"'
            );
        }

        /** @var ProvinceAddressConstraint $constraint */
        Assert::isInstanceOf($constraint, ProvinceAddressConstraint::class);

        $propertyPath = $this->context->getPropertyPath();

        foreach (iterator_to_array($this->context->getViolations()) as $violation) {
            if ('' === $propertyPath || 0 === strpos($violation->getPropertyPath(), $propertyPath)) {
                return;
            }
        }

        if (!$this->isProvinceValid($value)) {
            $this->context->addViolation($constraint->message);
        }
    }

    protected function isProvinceValid(AddressInterface $address): bool
    {
        return $this->provinceAddressChecker->isValid($address);
    }
}
