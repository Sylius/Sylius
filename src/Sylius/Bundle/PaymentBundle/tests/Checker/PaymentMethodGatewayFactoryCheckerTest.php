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

namespace Tests\Sylius\Bundle\PaymentBundle\Checker;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\PaymentBundle\Checker\PaymentMethodGatewayFactoryChecker;
use Sylius\Component\Payment\Checker\PaymentMethodGatewayFactoryCheckerInterface;
use Sylius\Component\Payment\Model\GatewayConfig;
use Sylius\Component\Payment\Model\PaymentMethod;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethodTranslation;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PaymentMethodGatewayFactoryCheckerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->entityManager = $entityManager;

        $this->createSchema();
    }

    protected function tearDown(): void
    {
        (new SchemaTool($this->entityManager))->dropDatabase();

        parent::tearDown();
    }

    #[Test]
    public function it_returns_true_when_a_payment_method_with_the_given_gateway_factory_exists(): void
    {
        $this->createPaymentMethodWithGatewayFactory('offline_payment', 'offline');

        $this->assertTrue($this->getChecker()->hasPaymentMethodWithGatewayFactory('offline'));
    }

    #[Test]
    public function it_returns_false_when_no_payment_method_with_the_given_gateway_factory_exists(): void
    {
        $this->createPaymentMethodWithGatewayFactory('offline_payment', 'offline');

        $this->assertFalse($this->getChecker()->hasPaymentMethodWithGatewayFactory('stripe'));
    }

    private function getChecker(): PaymentMethodGatewayFactoryCheckerInterface
    {
        /** @var PaymentMethodRepositoryInterface<PaymentMethodInterface> $paymentMethodRepository */
        $paymentMethodRepository = self::getContainer()->get('sylius.repository.payment_method');

        return new PaymentMethodGatewayFactoryChecker($paymentMethodRepository);
    }

    private function createPaymentMethodWithGatewayFactory(string $code, string $factoryName): void
    {
        $gatewayConfig = new GatewayConfig();
        $gatewayConfig->setGatewayName($code);
        $gatewayConfig->setFactoryName($factoryName);

        $paymentMethod = new PaymentMethod();
        $paymentMethod->setCode($code);
        $paymentMethod->setPosition(0);
        $paymentMethod->setGatewayConfig($gatewayConfig);

        $this->entityManager->persist($paymentMethod);
        $this->entityManager->flush();
    }

    private function createSchema(): void
    {
        $metadataFactory = $this->entityManager->getMetadataFactory();

        $metadata = [
            $metadataFactory->getMetadataFor(PaymentMethod::class),
            $metadataFactory->getMetadataFor(PaymentMethodTranslation::class),
            $metadataFactory->getMetadataFor(GatewayConfig::class),
        ];

        $eventManager = $this->entityManager->getEventManager();
        foreach ($eventManager->getListeners('postGenerateSchema') as $listener) {
            $eventManager->removeEventListener('postGenerateSchema', $listener);
        }

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }
}
