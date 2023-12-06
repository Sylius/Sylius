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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CorrectOrderAddressValidator extends ConstraintValidator
{
    public function __construct(private RepositoryInterface $countryRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var UpdateCart $value */
        Assert::isInstanceOf($value, UpdateCart::class);

        /** @var CorrectOrderAddress $constraint */
        Assert::isInstanceOf($constraint, CorrectOrderAddress::class);

        Assert::nullOrIsInstanceOf($value->billingAddress, AddressInterface::class);

        Assert::nullOrIsInstanceOf($value->shippingAddress, AddressInterface::class);

        $this->validateAddress($value->billingAddress, $constraint);
        $this->validateAddress($value->shippingAddress, $constraint);
    }

    private function validateAddress(?AddressInterface $address, CorrectOrderAddress $constraint): void
    {
        if ($address === null) {
            return;
        }

        /** @var string|null $countryCode */
        $countryCode = $address->getCountryCode();

        if ($countryCode === null) {
            $this->context->addViolation(
                $constraint->addressWithoutCountryCodeCanNotExistMessage,
            );

            return;
        }

        /** @var CountryInterface|null $country */
        $country = $this->countryRepository->findOneBy(['code' => $countryCode]);

        if ($country === null) {
            $this->context->addViolation(
                $constraint->countryCodeNotExistMessage,
                ['%countryCode%' => $countryCode],
            );
        }
    }
}
