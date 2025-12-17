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

namespace Sylius\Behat\Page;

use FriendsOfBehat\PageObjectExtension\Page\Page;

class ErrorPage extends Page implements ErrorPageInterface
{
    protected function getUrl(array $urlParameters = []): string
    {
        // This page does not have any url
        return '';
    }

    public function getCode(): int
    {
        return $this->getSession()->getStatusCode();
    }

    public function isItAdminNotFoundPage(): bool
    {
        return $this->getCode() === 404 && $this->hasElement('admin_back_to_dashboard_link');
    }

    public function isItShopNotFoundPage(): bool
    {
        return $this->getCode() === 404 && $this->hasElement('shop_not_found_page');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'admin_back_to_dashboard_link' => '[data-test-back-to-dashboard-link]',
            'shop_not_found_page' => '[data-test-shop-not-found-page]',
        ]);
    }
}
