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
use Sylius\Bundle\CoreBundle\Test\Services\PaymentMethodNameToGatewayConverterInterface;
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
     * @var PaymentMethodNameToGatewayConverterInterface
     */
    private $paymentMethodNameToGatewayConverter;

    /**
     * @param RepositoryInterface $paymentMethodRepository
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $paymentMethodFactory
     * @param PaymentMethodNameToGatewayConverterInterface $paymentMethodNameToGatewayConverter
     */
    public function __construct(
        RepositoryInterface $paymentMethodRepository,
        SharedStorageInterface $sharedStorage,
        FactoryInterface $paymentMethodFactory,
        PaymentMethodNameToGatewayConverterInterface $paymentMethodNameToGatewayConverter
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->paymentMethodNameToGatewayConverter = $paymentMethodNameToGatewayConverter;
    }

    /**
     * @Given the store allows paying :paymentMethodName
     */
    public function storeAllowsPaying($paymentMethodName)
    {
        $paymentMethod = $this->paymentMethodFactory->createNew();
        $paymentMethod->setCode('PM_'.$paymentMethodName);
        $paymentMethod->setName(ucfirst($paymentMethodName));
        $paymentMethod->setGateway($this->paymentMethodNameToGatewayConverter->convert($paymentMethodName));
        $paymentMethod->setDescription('Payment method');

        $channel = $this->sharedStorage->getCurrentResource('channel');
        $channel->addPaymentMethod($paymentMethod);

        $this->paymentMethodRepository->add($paymentMethod);
    }
}
