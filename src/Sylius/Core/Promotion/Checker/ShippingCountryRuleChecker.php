<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Promotion\Checker;

use Sylius\Addressing\Model\CountryInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Promotion\Checker\RuleCheckerInterface;
use Sylius\Promotion\Exception\UnsupportedTypeException;
use Sylius\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Repository\RepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShippingCountryRuleChecker implements RuleCheckerInterface
{
    const TYPE = 'shipping_country';

    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @param RepositoryInterface $countryRepository
     */
    public function __construct(RepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
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

        return $country->getId() === $configuration['country'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_shipping_country_configuration';
    }
}
