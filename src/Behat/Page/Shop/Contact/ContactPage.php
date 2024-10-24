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

namespace Sylius\Behat\Page\Shop\Contact;

use Sylius\Behat\Page\Shop\Page as ShopPage;

class ContactPage extends ShopPage implements ContactPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_contact_request';
    }

    public function send(): void
    {
        $this->getElement('send_button')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'email' => '[data-test-contact-email]',
            'message' => '[data-test-contact-message]',
            'send_button' => '[data-test-button="contact-send"]',
        ]);
    }
}
