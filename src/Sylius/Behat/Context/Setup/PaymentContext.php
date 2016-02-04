<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PaymentContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $paymentMethodFactory;

    /**
     * @param $paymentMethodRepository
     * @param $sharedStorage
     * @param $paymentMethodFactory
     */
    public function __construct(RepositoryInterface $paymentMethodRepository, SharedStorageInterface $sharedStorage, FactoryInterface $paymentMethodFactory)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodFactory = $paymentMethodFactory;
    }

    /**
     * @Given store allows paying offline
     */
    public function storeAllowsPayingOffline()
    {
        $paymentMethod = $this->paymentMethodFactory->createNew();
        $paymentMethod->setCode('PM1');
        $paymentMethod->setGateway('offline');
        $paymentMethod->setName('Offline');
        $paymentMethod->setDescription('Offline payment method');

        $channel = $this->sharedStorage->getCurrentResource('channel');
        $channel->addPaymentMethod($paymentMethod);

        $this->paymentMethodRepository->add($paymentMethod);
    }
}
