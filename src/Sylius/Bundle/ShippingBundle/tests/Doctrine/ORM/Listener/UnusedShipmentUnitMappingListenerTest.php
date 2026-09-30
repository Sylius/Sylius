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

namespace Tests\Sylius\Bundle\ShippingBundle\Doctrine\ORM\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShippingBundle\Doctrine\ORM\Listener\UnusedShipmentUnitMappingListener;
use Sylius\Component\Shipping\Model\Shipment;
use Sylius\Component\Shipping\Model\ShipmentUnit;

#[CoversClass(UnusedShipmentUnitMappingListener::class)]
final class UnusedShipmentUnitMappingListenerTest extends TestCase
{
    public function test_it_removes_the_shipment_association_when_an_unrelated_shipment_unit_model_is_configured(): void
    {
        $metadata = $this->createShipmentUnitMetadata();

        (new UnusedShipmentUnitMappingListener(Shipment::class))->loadClassMetadata($this->createEvent($metadata));

        $this->assertFalse($metadata->hasAssociation('shipment'));
    }

    public function test_it_keeps_the_shipment_association_when_the_shipment_unit_itself_is_configured(): void
    {
        $metadata = $this->createShipmentUnitMetadata();

        (new UnusedShipmentUnitMappingListener(ShipmentUnit::class))->loadClassMetadata($this->createEvent($metadata));

        $this->assertTrue($metadata->hasAssociation('shipment'));
    }

    public function test_it_keeps_the_shipment_association_when_a_subclass_of_the_shipment_unit_is_configured(): void
    {
        $metadata = $this->createShipmentUnitMetadata();
        $shipmentUnitSubclass = new class() extends ShipmentUnit {
        };

        (new UnusedShipmentUnitMappingListener($shipmentUnitSubclass::class))->loadClassMetadata($this->createEvent($metadata));

        $this->assertTrue($metadata->hasAssociation('shipment'));
    }

    public function test_it_does_not_touch_other_classes(): void
    {
        $metadata = new ClassMetadata(Shipment::class);
        $metadata->mapManyToOne(['fieldName' => 'shipment', 'targetEntity' => Shipment::class]);

        (new UnusedShipmentUnitMappingListener(Shipment::class))->loadClassMetadata($this->createEvent($metadata));

        $this->assertTrue($metadata->hasAssociation('shipment'));
    }

    private function createShipmentUnitMetadata(): ClassMetadata
    {
        $metadata = new ClassMetadata(ShipmentUnit::class);
        $metadata->isMappedSuperclass = true;
        $metadata->mapManyToOne([
            'fieldName' => 'shipment',
            'targetEntity' => Shipment::class,
            'inversedBy' => 'units',
        ]);

        return $metadata;
    }

    private function createEvent(ClassMetadata $metadata): LoadClassMetadataEventArgs
    {
        return new LoadClassMetadataEventArgs($metadata, $this->createStub(EntityManagerInterface::class));
    }
}
