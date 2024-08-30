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

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class ShippingCountryRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'shipping_country';

    public function __construct(private RepositoryInterface $countryRepository)
    {
    }

    /**
     * @throws UnsupportedTypeException
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        if (null === $address = $subject->getShippingAddress()) {
            return false;
        }

        $country = $this->countryRepository->findOneBy(['code' => $address->getCountryCode()]);

        if (!$country instanceof CountryInterface) {
            return false;
        }

        return $country->getCode() === $configuration['country'];
    }
}
