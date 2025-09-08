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

namespace Sylius\Behat\Page\Shop\PaymentRequest;

use Behat\Mink\Driver\BrowserKitDriver;
use Sylius\Behat\Page\SymfonyPage;
use Symfony\Component\BrowserKit\AbstractBrowser;

class PaymentRequestNotifyPage extends SymfonyPage implements PaymentRequestNotifyPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_payment_request_notify';
    }

    public function openWithClient(
        string $method,
        array $urlParameters = [],
        array $files = [],
        array $server = [],
        ?string $content = null,
    ): void {
        $client = $this->getClient();
        $client->request(
            method: $method,
            uri: $this->getUrl($urlParameters),
            files: $files,
            server: $server,
            content: $content,
        );
    }

    public function getClient(): AbstractBrowser
    {
        $driver = $this->getDriver();
        if ($driver instanceof BrowserKitDriver) {
            return $driver->getClient();
        }

        throw new \LogicException(sprintf('This page require a "%s" driver.', BrowserKitDriver::class));
    }
}
