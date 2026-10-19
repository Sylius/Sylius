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

namespace Sylius\Tests\Functional\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PaymentRequestRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    /** @var PaymentRequestRepositoryInterface<PaymentRequestInterface> */
    private PaymentRequestRepositoryInterface $repository;

    /** @var PaymentRequestFactoryInterface<PaymentRequestInterface> */
    private PaymentRequestFactoryInterface $factory;

    private PaymentMethodInterface $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->repository = self::getContainer()->get('sylius.repository.payment_request');
        $this->factory = self::getContainer()->get('sylius.factory.payment_request');

        $fixtures = $this->loadFixtures();
        $this->paymentMethod = $fixtures['cash_on_delivery'];
    }

    #[Test]
    public function it_returns_the_latest_payment_request_when_several_match(): void
    {
        $payment = $this->createPayment();
        $this->createPaymentRequest($payment, PaymentRequestInterface::ACTION_CAPTURE, new \DateTime('-1 minute'));
        $latestHash = $this->createPaymentRequest($payment, PaymentRequestInterface::ACTION_CAPTURE, new \DateTime())->getHash();

        $paymentRequest = $this->repository->findOneByActionPaymentAndMethod(
            PaymentRequestInterface::ACTION_CAPTURE,
            $payment,
            $this->paymentMethod,
        );

        self::assertNotNull($paymentRequest);
        self::assertEquals($latestHash, $paymentRequest->getHash());
    }

    #[Test]
    public function it_returns_the_only_matching_payment_request(): void
    {
        $payment = $this->createPayment();
        $hash = $this->createPaymentRequest($payment, PaymentRequestInterface::ACTION_CAPTURE, new \DateTime())->getHash();

        $paymentRequest = $this->repository->findOneByActionPaymentAndMethod(
            PaymentRequestInterface::ACTION_CAPTURE,
            $payment,
            $this->paymentMethod,
        );

        self::assertNotNull($paymentRequest);
        self::assertEquals($hash, $paymentRequest->getHash());
    }

    #[Test]
    public function it_returns_null_when_no_payment_request_matches_the_action(): void
    {
        $payment = $this->createPayment();
        $this->createPaymentRequest($payment, PaymentRequestInterface::ACTION_CAPTURE, new \DateTime());

        self::assertNull($this->repository->findOneByActionPaymentAndMethod(
            PaymentRequestInterface::ACTION_AUTHORIZE,
            $payment,
            $this->paymentMethod,
        ));
    }

    private function createPayment(): PaymentInterface
    {
        $order = new Order();
        $order->setCurrencyCode('USD');
        $order->setLocaleCode('en_US');
        $this->entityManager->persist($order);

        $payment = new Payment();
        $payment->setCurrencyCode('USD');
        $payment->setOrder($order);
        $payment->setMethod($this->paymentMethod);
        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $payment;
    }

    private function createPaymentRequest(
        PaymentInterface $payment,
        string $action,
        \DateTimeInterface $createdAt,
    ): PaymentRequestInterface {
        $paymentRequest = $this->factory->create($payment, $this->paymentMethod);
        $paymentRequest->setAction($action);
        $paymentRequest->setCreatedAt($createdAt);

        $this->repository->add($paymentRequest);

        return $paymentRequest;
    }

    /** @return array<string, object> */
    private function loadFixtures(): array
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        return $fixtureLoader->load([
            __DIR__ . '/../../DataFixtures/ORM/resources/channels.yml',
            __DIR__ . '/../../DataFixtures/ORM/resources/payment_methods.yml',
        ], [], [], PurgeMode::createDeleteMode());
    }
}
