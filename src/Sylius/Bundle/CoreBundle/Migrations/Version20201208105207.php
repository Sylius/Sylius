<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Version20201208105207 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getDescription() : string
    {
        return 'Set details and shipment_id on shipping adjustments.';
    }

    public function up(Schema $schema) : void
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

    public function down(Schema $schema) : void
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
                SELECT sa.id, sa.label, sa.order_id, s.id as shipping_id, s.method_id, ssm.code AS shipment_code
                FROM sylius_adjustment sa
                JOIN sylius_shipment s ON s.order_id = sa.order_id
                JOIN sylius_shipping_method ssm on s.method_id = ssm.id
                WHERE sa.type = "shipping"
            '
        );
    }
}
