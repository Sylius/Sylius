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

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class GeneratePage extends SymfonyPage implements GeneratePageInterface
{
    public function generate(): void
    {
        $this->getElement('generate_button')->press();
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_promotion_coupon_generate';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'generate_button' => '[data-test-generate-button]',
        ]);
    }
}
