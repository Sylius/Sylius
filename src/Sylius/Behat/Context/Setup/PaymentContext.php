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
use Proxies\__CG__\Sylius\Component\Payment\Model\PaymentMethod;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaymentContext implements Context
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
     * @param RepositoryInterface $paymentMethodRepository
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $paymentMethodFactory
     */
    public function __construct(RepositoryInterface $paymentMethodRepository, SharedStorageInterface $sharedStorage, FactoryInterface $paymentMethodFactory)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodFactory = $paymentMethodFactory;
    }

    /**
     * @Given store allows paying :paymentMethodName
     */
    public function storeAllowsPaying($paymentMethodName)
    {
        $properties = [
            'code' => 'PM1',
            'name' => $paymentMethodName,
            'description' => 'Payment method'
        ];

        $paymentMethod = $this->paymentMethodFactory->createFromArray($properties);

        $channel = $this->sharedStorage->getCurrentResource('channel');
        $channel->addPaymentMethod($paymentMethod);

        $this->paymentMethodRepository->add($paymentMethod);
    }
}
