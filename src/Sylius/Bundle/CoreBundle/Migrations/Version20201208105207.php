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

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Version20201208105207 extends AbstractMigration
{
    /** @var ContainerInterface */
    private $container;

    public function getDescription(): string
    {
        return 'Set details and shipment_id on shipping adjustments.';
    }

    public function up(Schema $schema): void
    {
        $this->setDefaultAdjustmentData();

        $adjustments = $this->getShipmentAdjustmentsWithData();

        foreach ($adjustments as $adjustment) {
            $this->updateAdjustment(
                (int) $adjustment['id'],
                (int) $adjustment['shipping_id'],
                json_encode(['shippingMethodCode' => $adjustment['shipment_code'], 'shippingMethodName' => $adjustment['label']])
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->setDefaultAdjustmentData();
    }

    private function setDefaultAdjustmentData(): void
    {
        $this->connection->executeQuery("UPDATE sylius_adjustment SET shipment_id = null, details = '[]'");
    }

    private function updateAdjustment(int $adjustmentId, int $shipmentId, string $details): void
    {
        $this->connection->executeQuery("UPDATE sylius_adjustment SET shipment_id = $shipmentId, details = '" . $details . "' WHERE id = $adjustmentId");
    }

    private function getShipmentAdjustmentsWithData(): array
    {
       return $this->connection->fetchAllAssociative(
            '
                SELECT adjustment.id, adjustment.label, adjustment.order_id, shipment.id as shipping_id, shipment.method_id, shipping_method.code AS shipment_code
                FROM sylius_adjustment adjustment
                JOIN sylius_shipment shipment ON shipment.order_id = adjustment.order_id
                JOIN sylius_shipping_method shipping_method on shipment.method_id = shipping_method.id
                WHERE adjustment.type = "shipping"
            '
        );
    }
}
