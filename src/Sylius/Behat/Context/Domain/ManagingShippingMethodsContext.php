<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ManagingShippingMethodsContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var ObjectManager
     */
    private $shippingMethodManager;

    /**
     * @param RepositoryInterface $shippingMethodRepository
     * @param ObjectManager $shippingMethodManager
     */
    public function __construct(RepositoryInterface $shippingMethodRepository, ObjectManager $shippingMethodManager)
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->shippingMethodManager = $shippingMethodManager;
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
