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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\AddressOrder;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChoosePaymentMethod;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CheckoutContext implements Context
{
    /** @var RepositoryInterface */
    private $shippingMethodRepository;

    /** @var RepositoryInterface */
    private $paymentMethodRepository;

    /** @var MessageBusInterface */
    private $commandBus;

    /** @var FactoryInterface */
    private $addressFactory;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        RepositoryInterface $shippingMethodRepository,
        RepositoryInterface $paymentMethodRepository,
        MessageBusInterface $commandBus,
        FactoryInterface $addressFactory,
        SharedStorageInterface $sharedStorage
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->commandBus = $commandBus;
        $this->addressFactory = $addressFactory;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I have proceeded through checkout process
     */
    public function iHaveProceededThroughCheckoutProcess(): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setCity('New York');
        $address->setStreet('Wall Street');
        $address->setPostcode('00-001');
        $address->setCountryCode('US');
        $address->setFirstName('Richy');
        $address->setLastName('Rich');

        $command = new AddressOrder('rich@sylius.com', $address);
        $command->setOrderTokenValue($this->sharedStorage->get('cart_token'));
        $this->commandBus->dispatch($command);

        $command = new ChooseShippingMethod(0, $this->shippingMethodRepository->findOneBy([])->getCode());
        $command->setOrderTokenValue($this->sharedStorage->get('cart_token'));
        $this->commandBus->dispatch($command);

        $command = new ChoosePaymentMethod(0, $this->paymentMethodRepository->findOneBy([])->getCode());
        $command->setOrderTokenValue($this->sharedStorage->get('cart_token'));
        $this->commandBus->dispatch($command);
    }
}
