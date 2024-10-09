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

use FriendsOfBehat\PageObjectExtension\Page\PageInterface as BasePageInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;

interface PaymentRequestNotifyPageInterface extends BasePageInterface
{
    /**
     * Calls a URI.
     *
     * @param string      $method        The request method
     * @param array       $urlParameters The url parameters
     * @param array       $files         The files
     * @param array       $server        The server parameters (HTTP headers are referenced with an HTTP_ prefix as PHP does)
     * @param string|null $content       The raw body data
     */
    public function openWithClient(
        string $method,
        array $urlParameters = [],
        array $files = [],
        array $server = [],
        ?string $content = null,
    ): void;

    public function getClient(): AbstractBrowser;
}
