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

namespace Sylius\Tests\Functional\Encryption;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PaymentRequestEncryptionTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    /** @var PaymentRequestRepositoryInterface<PaymentRequestInterface> */
    private PaymentRequestRepositoryInterface $paymentRequestRepository;

    /** @var PaymentRequestFactoryInterface<PaymentRequestInterface> */
    private PaymentRequestFactoryInterface $paymentRequestFactory;

    private array $fixtures;

    public function setUp(): void
    {
        $this->entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->paymentRequestRepository = self::getContainer()->get('sylius.repository.payment_request');
        $this->paymentRequestFactory = self::getContainer()->get('sylius.factory.payment_request');

        $this->fixtures = $this->loadFixtures([
            __DIR__ . '/../../DataFixtures/ORM/resources/channels.yml',
            __DIR__ . '/../../DataFixtures/ORM/resources/payment_methods.yml',
        ]);
    }

    /**
     * @test
     *
     * @dataProvider getPayload
     */
    public function it_covers_encryption_and_decryption_when_saving_and_loading_the_payload(mixed $payload): void
    {
        $paymentRequest = $this->createTestPaymentRequest();
        $paymentRequest->setPayload($payload);

        $this->paymentRequestRepository->add($paymentRequest);
        $hash = $paymentRequest->getHash();
        self::assertEquals($payload, $paymentRequest->getPayload());

        $this->entityManager->clear();

        $payloadFromDatabase = unserialize($this->getDatabaseColumnDataForHash('payload'));
        self::assertNotEquals($paymentRequest->getPayload(), $payloadFromDatabase);

        $paymentRequestFromRepository = $this->paymentRequestRepository->find($hash);
        self::assertEquals($paymentRequest->getPayload(), $paymentRequestFromRepository->getPayload());
        self::assertEquals($payload, $paymentRequest->getPayload());

        $payloadFromDatabase = unserialize($this->getDatabaseColumnDataForHash('payload'));
        self::assertNotEquals($paymentRequest->getPayload(), $payloadFromDatabase);
        self::assertNotEquals($payload, $payloadFromDatabase);
    }

    /** @test */
    public function it_not_encrypt_and_decrypt_null_payloads(): void
    {
        $paymentRequest = $this->createTestPaymentRequest();
        $paymentRequest->setPayload(null);

        $this->paymentRequestRepository->add($paymentRequest);
        $hash = $paymentRequest->getHash();
        self::assertNull($paymentRequest->getPayload());

        $this->entityManager->clear();

        $payloadFromDatabase = unserialize($this->getDatabaseColumnDataForHash('payload'));
        self::assertSame($paymentRequest->getPayload(), $payloadFromDatabase);

        $paymentRequestFromRepository = $this->paymentRequestRepository->find($hash);
        self::assertSame($paymentRequest->getPayload(), $paymentRequestFromRepository->getPayload());
        self::assertNull($paymentRequest->getPayload());

        $payloadFromDatabase = unserialize($this->getDatabaseColumnDataForHash('payload'));
        self::assertEquals($paymentRequest->getPayload(), $payloadFromDatabase);
        self::assertNull($payloadFromDatabase);
    }

    /**
     * @test
     *
     * @dataProvider getResponseData
     */
    public function it_covers_encryption_and_decryption_when_saving_and_loading_the_response_data(
        array $responseData,
    ): void {
        $paymentRequest = $this->createTestPaymentRequest();
        $paymentRequest->setResponseData($responseData);

        $this->paymentRequestRepository->add($paymentRequest);
        $hash = $paymentRequest->getHash();
        self::assertEquals($responseData, $paymentRequest->getResponseData());

        $this->entityManager->clear();

        $responseDataFromDatabase = json_decode($this->getDatabaseColumnDataForHash('response_data'), true);
        self::assertNotEquals($paymentRequest->getResponseData(), $responseDataFromDatabase);

        $paymentRequestFromRepository = $this->paymentRequestRepository->find($hash);
        self::assertEquals($paymentRequest->getResponseData(), $paymentRequestFromRepository->getResponseData());
        self::assertEquals($responseData, $paymentRequest->getResponseData());

        $responseDataFromDatabase = json_decode($this->getDatabaseColumnDataForHash('response_data'), true);
        self::assertNotEquals($paymentRequest->getResponseData(), $responseDataFromDatabase);
        self::assertNotEquals($responseData, $responseDataFromDatabase);
    }

    public static function getPayload(): iterable
    {
        yield 'integer payload' => [42];
        yield 'string payload' => ['payload'];
        yield 'array payload' => [['key' => 'value']];
        yield 'object payload' => [new \stdClass()];
    }

    public static function getResponseData(): iterable
    {
        yield 'integer response data' => [[42]];
        yield 'string response data' => [['response_data']];
        yield 'array response data' => [['key' => 'value']];
        yield 'object response data' => [[new \stdClass()]];
        yield 'complex response data' => [[
            'some_other_data' => 'data',
            'some_object' => new \stdClass(),
            'http_request' => [
                'query' => '?token=123',
                'request' => 'smth',
                'method' => 'GET',
                'uri' => 'http://example.com',
                'client_ip' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0',
                'content' => 'content',
                'headers' => ['Content-Type' => 'application/json'],
            ],
        ]];
    }

    private function createTestPaymentRequest(): PaymentRequestInterface
    {
        $order = new Order();
        $order->setCurrencyCode('USD');
        $order->setLocaleCode('en_US');
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $payment = new Payment();
        $payment->setCurrencyCode('USD');
        $payment->setOrder($order);
        $payment->setMethod($this->fixtures['cash_on_delivery']);
        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $this->paymentRequestFactory->create($payment, $payment->getMethod());
    }

    private function getDatabaseColumnDataForHash(string $column): mixed
    {
        $result = $this->entityManager->getConnection()->executeQuery(
            'SELECT * FROM sylius_payment_request LIMIT 1',
        );

        return $result->fetchAssociative()[$column];
    }

    private function loadFixtures(array $fixtureFiles): array
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        return $fixtureLoader->load($fixtureFiles, [], [], PurgeMode::createDeleteMode());
    }
}
