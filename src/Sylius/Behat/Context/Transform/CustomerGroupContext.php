<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class CustomerGroupContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $customerGroupRepository;

    /**
     * @param RepositoryInterface $customerGroupRepository
     */
    public function __construct(RepositoryInterface $customerGroupRepository)
    {
        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * @Transform :customerGroup
     * @Transform /^group "([^"]+)"$/
     * @Transform /^"([^"]+)" group$/
     */
    public function getCustomerGroupByName($customerGroupName)
    {
        $customerGroup = $this->customerGroupRepository->findOneBy(['name' => $customerGroupName]);

        Assert::notNull($customerGroup, sprintf('Cannot find customer group with name %s', $customerGroupName));

        return $customerGroup;
    }
}
