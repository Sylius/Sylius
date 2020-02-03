<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Shipment;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Component\Core\Model\ProductInterface;

final class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_admin_shipment_show';
    }

    public function hasShipmentUnit(int $amountOf, ProductInterface $product): bool
    {
        $table = $this->getElement('shipment_table');

        return $amountOf === count($table->findAll('css', sprintf('td:contains("%s")', $product->getName())));
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'shipment_table' => 'table tbody',
        ]);
    }
}
