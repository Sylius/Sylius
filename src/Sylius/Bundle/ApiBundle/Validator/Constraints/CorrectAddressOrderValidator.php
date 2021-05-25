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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\Checkout\AddressOrder;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CorrectAddressOrderValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /** @var AddressOrder $value */
        Assert::isInstanceOf($value, AddressOrder::class);

        /** @var CorrectAddressOrder $constraint */
        Assert::isInstanceOf($constraint, CorrectAddressOrder::class);

        $this->validateAddress($value->billingAddress, $constraint);
        $this->validateAddress($value->shippingAddress, $constraint);
    }

    private function validateAddress(?AddressInterface $address, CorrectAddressOrder $constraint): void
    {
        if ($address === null) {
            return;
        }

        $countryCode = $address->getCountryCode();

        if (!Countries::exists($countryCode)) {
            $this->context->addViolation(
                $constraint->countryWithCountryCodeNotExistMessage,
                ['%countryCode%' => $countryCode]
            );
        }
    }
}
