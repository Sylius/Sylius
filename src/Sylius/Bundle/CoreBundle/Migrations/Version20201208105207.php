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

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMigration;

final class Version20201208105207 extends AbstractMigration
{
    private array $shippingName = [];

    public function getDescription(): string
    {
        return 'Set details and shipment_id on shipping adjustments.';
    }

    public function up(Schema $schema): void
    {
        $this->setDefaultAdjustmentData();

        $adjustments = $this->getShipmentAdjustmentsWithData();

        foreach ($adjustments as $adjustment) {
            if (!isset($this->shippingName[$adjustment['shipment_code']])) {
                $this->shippingName[$adjustment['shipment_code']] = $adjustment['label'];
            }

            $this->updateAdjustment(
                (int) $adjustment['id'],
                isset($adjustment['shipping_id']) ? (string) $adjustment['shipping_id'] : 'NULL',
                $this->getParsedDetails(['taxRateCode' => $adjustment['tax_rate_code'], 'taxRateName' => $adjustment['tax_rate_name'], 'taxRateAmount' => ($adjustment['tax_rate_amount'] ? (float) $adjustment['tax_rate_amount'] : null), 'shippingMethodCode' => $adjustment['shipment_code'], 'shippingMethodName' => $this->getShippingMethodName($adjustment['shipment_code'])]),
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->setDefaultAdjustmentData();
    }

    private function getShippingMethodName(?string $shippingMethodCode)
    {
        if ($shippingMethodCode === null) {
            return $shippingMethodCode;
        }

        return $this->shippingName[$shippingMethodCode];
    }

    private function getParsedDetails(array $details): string
    {
        /** @var array $parsedDetails */
        $parsedDetails = [];

        foreach ($details as $key => $value) {
            if ($value !== null) {
                $parsedDetails[$key] = $value;
            }
        }

        return json_encode($parsedDetails);
    }

    private function setDefaultAdjustmentData(): void
    {
        $this->addSql("UPDATE sylius_adjustment SET shipment_id = null, details = '[]'");
    }

    private function updateAdjustment(int $adjustmentId, string $shipmentId, string $details): void
    {
        $this->addSql("UPDATE sylius_adjustment SET shipment_id = $shipmentId, details = '" . $details . "' WHERE id = $adjustmentId");
    }

    private function getShipmentAdjustmentsWithData(): array
    {
        return $this->connection->fetchAllAssociative(
            '
                SELECT adjustment.id, adjustment.label, tax_rate.code as tax_rate_code, tax_rate.name as tax_rate_name, tax_rate.amount as tax_rate_amount, adjustment.order_id, shipment.id as shipping_id, shipment.method_id, shipping_method.code AS shipment_code
                FROM sylius_adjustment adjustment
                LEFT JOIN sylius_shipment shipment ON shipment.order_id = adjustment.order_id
                LEFT JOIN sylius_shipping_method shipping_method on shipment.method_id = shipping_method.id
                LEFT JOIN sylius_tax_rate tax_rate on adjustment.label LIKE CONCAT(tax_rate.name, \'%\')
                WHERE adjustment.type IN ("shipping", "tax")
                ORDER BY adjustment.type
            ',
        );
    }
}
