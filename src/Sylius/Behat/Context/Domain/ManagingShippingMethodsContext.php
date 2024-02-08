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

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Webmozart\Assert\Assert;

final class ManagingShippingMethodsContext implements Context
{
    public function __construct(
        private RepositoryInterface $shippingMethodRepository,
        private ObjectManager $shippingMethodManager,
    ) {
    }

    /**
     * @When /^I archive the ("[^"]+" shipping method)$/
     */
    public function iArchiveTheShippingMethod(ShippingMethodInterface $shippingMethod)
    {
        $shippingMethod->setArchivedAt(new \DateTime());

        $this->shippingMethodManager->flush();
    }

    /**
     * @Then the shipping method :shippingMethod should still exist in the registry
     */
    public function theShippingMethodShouldStillExistInTheRegistry(ShippingMethodInterface $shippingMethod)
    {
        Assert::notNull($this->shippingMethodRepository->find($shippingMethod));
    }
}
