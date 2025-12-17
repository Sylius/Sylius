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

namespace Sylius\Bundle\CoreBundle\Tests\Telemetry\Provider\Business;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\PaymentMethodsData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\PaymentMethodsDataProvider;

final class PaymentMethodsDataProviderTest extends TestCase
{
    private Connection $connection;

    private PaymentMethodsDataProvider $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->provider = new PaymentMethodsDataProvider($this->connection);
    }

    public function test_it_provides_active_payment_providers_with_details(): void
    {
        $this->connection->method('fetchAllAssociative')->willReturn([
            ['code' => 'paypal_checkout', 'factory_name' => 'payum_paypal', 'payments_count' => 15000],
            ['code' => 'stripe_payment', 'factory_name' => 'payum_stripe', 'payments_count' => 850],
            ['code' => 'cash_on_delivery', 'factory_name' => 'offline', 'payments_count' => 0],
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(PaymentMethodsData::class, $data);
        self::assertCount(3, $data->paymentProviders);

        self::assertSame('paypal_checkout', $data->paymentProviders[0]->name);
        self::assertSame('payum_paypal', $data->paymentProviders[0]->gateway);
        self::assertSame('10K-100K', $data->paymentProviders[0]->paymentsCount);

        self::assertSame('stripe_payment', $data->paymentProviders[1]->name);
        self::assertSame('payum_stripe', $data->paymentProviders[1]->gateway);
        self::assertSame('100-1K', $data->paymentProviders[1]->paymentsCount);

        self::assertSame('cash_on_delivery', $data->paymentProviders[2]->name);
        self::assertSame('offline', $data->paymentProviders[2]->gateway);
        self::assertSame('0-100', $data->paymentProviders[2]->paymentsCount);
    }

    public function test_it_returns_empty_array_on_error(): void
    {
        $this->connection->method('fetchAllAssociative')->willThrowException(new \RuntimeException('Database error'));

        $data = $this->provider->provide();

        self::assertInstanceOf(PaymentMethodsData::class, $data);
        self::assertSame([], $data->paymentProviders);
    }

    public function test_it_returns_empty_array_when_no_payment_methods(): void
    {
        $this->connection->method('fetchAllAssociative')->willReturn([]);

        $data = $this->provider->provide();

        self::assertInstanceOf(PaymentMethodsData::class, $data);
        self::assertSame([], $data->paymentProviders);
    }
}
