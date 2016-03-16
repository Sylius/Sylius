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
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PaymentContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @var FactoryInterface
     */
    private $paymentMethodFactory;

    /**
     * @var PaymentMethodNameToGatewayConverterInterface
     */
    private $paymentMethodNameToGatewayConverter;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $paymentMethodRepository
     * @param FactoryInterface $paymentMethodFactory
     * @param PaymentMethodNameToGatewayConverterInterface $paymentMethodNameToGatewayConverter
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $paymentMethodRepository,
        FactoryInterface $paymentMethodFactory,
        PaymentMethodNameToGatewayConverterInterface $paymentMethodNameToGatewayConverter
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->paymentMethodNameToGatewayConverter = $paymentMethodNameToGatewayConverter;
    }

    /**
     * @Given the store allows paying :paymentMethodName
     * @Given the store allows paying with :paymentMethodName
     */
    public function storeAllowsPaying($paymentMethodName)
    {
        $paymentMethod = $this->paymentMethodFactory->createNew();
        $paymentMethod->setCode('PM_'.$paymentMethodName);
        $paymentMethod->setName(ucfirst($paymentMethodName));
        $paymentMethod->setGateway($this->paymentMethodNameToGatewayConverter->convert($paymentMethodName));
        $paymentMethod->setDescription('Payment method');

        $channel = $this->sharedStorage->get('channel');
        $channel->addPaymentMethod($paymentMethod);

        $this->paymentMethodRepository->add($paymentMethod);
    }
}
