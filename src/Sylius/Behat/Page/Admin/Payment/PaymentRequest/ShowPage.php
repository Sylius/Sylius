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

namespace Sylius\Behat\Page\Admin\Payment\PaymentRequest;

use Sylius\Behat\Page\SymfonyPage;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_admin_payment_request_show';
    }

    public function getFieldText(string $fieldName): string
    {
        return $this->getElement($fieldName)->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'action' => '[data-test-action]',
            'method' => '[data-test-method]',
            'payload' => '[data-test-payload]',
            'response_data' => '[data-test-response-data]',
            'state' => '[data-test-state]',
        ]);
    }
}
