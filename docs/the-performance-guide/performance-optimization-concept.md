---
layout:
  width: default
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
  metadata:
    visible: true
---

# Performance Optimization Concept

This document describes a refactor concept of Sylius' default `Order → OrderItem → OrderItemUnit` hierarchy into a **2-layer model** with unit data embedded in JSON on `OrderItem`.\
It also details a custom Doctrine subscriber that uses the **Unit of Work (UoW)** to **detach** unit entities before flush and **rehydrate** them afterward—removing ORM overhead without breaking domain logic.

***

### 1. Problem: Performance Drawbacks of the 3‑Layer Model

| Issue                     | Description                                                                                                                                                                                |
| ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| N+1 Query Overhead        | Fetching an `Order` with its items and individual units typically requires iterating over each `OrderItem` and querying its units separately—yielding dozens or hundreds of extra queries. |
| Expensive Joins           | Retrieving the full order tree involves multiple JOINs across three tables, slowing reads—especially in large datasets or under load.                                                      |
| Hydration Memory Overhead | Doctrine (or similar ORMs) must instantiate and hydrate each `OrderItemUnit` as an object, consuming CPU and RAM.                                                                          |

**Illustrative Example (3‑Layer Fetch):**

```sql
-- ORM may generate:
SELECT o.*, oi.*, oi_u.*
FROM orders o
JOIN order_items oi ON oi.order_id = o.id
JOIN order_item_units oi_u ON oi_u.order_item_id = oi.id
WHERE o.id = :orderId;
```

**Possible N+1:**

```php
$order = orderRepository->find($id);
foreach ($order->getItems() as $item) {
    $units = $item->getUnits(); // May trigger additional query per item
}
```

***

## 2. Optimized Structure: Flatten Units into JSON

#### ✅ What Changes

| Component       | Default Sylius (3-Layer)          | Optimized Version (2-Layer with JSON)                  |
| --------------- | --------------------------------- | ------------------------------------------------------ |
| `OrderItemUnit` | Stored in a separate table        | Removed from DB; serialized into `unitsData` JSON      |
| `OrderItem`     | Holds references to unit entities | Adds `units_data` JSONB column with embedded unit data |
| Adjustments     | Entities with relations           | Stored inline in JSON using `toArray()`                |

***

## Modified Entities

```php
// src/Entity/Order/OrderItem.php

#[ORM\Column(name: "units_data", type: "json", options: ['jsonb' => true])]
private $unitsData = [];

public function getUnitsData(): array {
    return $this->unitsData ?? [];
}

public function syncItemsData(): void {
    $this->unitsData = [];
    foreach ($this->units as $unit) {
        $this->unitsData[] = $unit->toArray();
    }
}

public function clearUnits(): void {
    $this->units->clear();
}
```

```php
// src/Entity/Order/OrderItemUnit.php

public function __construct(OrderItem $orderItem) {
    parent::__construct($orderItem);
    $this->id = uuid_create();
}

public function toArray(): array {
    return [
        'id' => $this->id,
        'adjustments' => array_map(fn($a) => $a->toArray(), $this->adjustments->toArray()),
        'shipmentId' => $this->shipment?->getId(),
    ];
}
```

```php
// src/Entity/Order/Adjustment.php

public function toArray(): array {
    return [
        'id' => $this->id ?? uuid_create(),
        'amount' => $this->amount,
        'type' => $this->type,
        'label' => $this->label,
        'orderItemUnit' => $this->orderItemUnit?->getId(),
    ];
}
```

***

### 2.1. Mechanism: Full Lifecycle Control via Doctrine UoW

This optimization could be powered by a **custom Doctrine subscriber** (`OrderItemFlushSubscriber`) that manages unit entities manually.

#### 📦 PreFlush — Serialize & Detach

* **Serialize units** to JSON (`syncItemsData()`).
* **Backup** current units in memory.
* **Detach** `OrderItemUnit` and related `Adjustment` entities from UoW so Doctrine does not persist or hydrate them.

```php
// example pseudocode

public function preFlush(PreFlushEventArgs $args): void {
    $em  = $args->getObjectManager();
    $uow = $em->getUnitOfWork();
    $targets = array_merge(
        array_filter($uow->getScheduledEntityInsertions(), static fn ($e) => $e instanceof OrderItem),
        array_filter($uow->getScheduledEntityUpdates(),    static fn ($e) => $e instanceof OrderItem),
    );

    foreach ($targets as $item) {
        if ($item instanceof OrderItem) {
            $item->syncItemsData();
            $this->backup[spl_object_id($item)] = $item->getUnits()->toArray();
            $item->clearUnits();

            foreach ($this->backup[spl_object_id($item)] as $unit) {
                foreach ($unit->getAdjustments() as $adj) {
                    $this->unschedule($uow, $adj);
                }
                $this->unschedule($uow, $unit);
            }
        }
    }
}

....

private function unschedule(UnitOfWork $uow, object $entity): void {
    if ($uow->getEntityState($entity) === UnitOfWork::STATE_NEW) {
        // Remove from insertions + change state to DETACHED
    } else {
        $uow->detach($entity); // Remove from managed state
    }
}
```

#### 🚫 OnFlush — Skip Persisting Units

Ensures no unit or adjustment entities end up in the SQL queue.

```php
// example pseudocode

public function onFlush(OnFlushEventArgs $args): void {
    $em  = $args->getObjectManager();
    $uow = $em->getUnitOfWork();

    foreach (array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates()) as $entity) {
        if ($entity instanceof OrderItemUnit || $entity instanceof Adjustment) {
            $em->detach($entity);
        }
    }
}
```

#### 🔄 PostFlush — Restore for In-Memory Logic ( if direct access needed for example in behat tests )

Reattaches units and shipments in memory for continued processing after DB flush.

```php
// example pseudocode

public function postFlush(PostFlushEventArgs $args): void {
    $em  = $args->getObjectManager();
    $uow = $em->getUnitOfWork();

    foreach ($uow->getIdentityMap()[OrderItem::class] ?? [] as $item) {
        $oid = spl_object_id($item);
        if (!empty($this->backup[$oid])) {
            $item->clearUnits();
            foreach ($this->backup[$oid] as $unit) {
                $item->addUnit($unit);
            }
        }
    }
    $this->backup = [];
}
```

#### ✅ Why This Works

* Ensures that only the `OrderItem` entity is flushed.
* Prevents unneeded updates or inserts to the `order_item_units` table.
* Reduces DB overhead and RAM consumption by skipping unit hydration entirely.

***

### 2.2. Rebuilding the Objects on `postLoad`

This section is strictly about restoring in-memory `OrderItemUnit` objects using the JSON stored in `units_data`. These are isolated from Doctrine’s UoW and used purely for domain logic operations.

**Responsibilities:**

* Deserialize `units_data`.
* Instantiate `OrderItemUnit` objects and set their IDs from the JSON.
* Add them back to their `OrderItem`.

```php
// example pseudocode

public function postLoad(LifecycleEventArgs $args): void {
    $entity = $args->getObject();

    if (!$entity instanceof OrderItem) {
        return;
    }

    foreach ($entity->getUnitsData() as $unitArray) {
        $unit = new OrderItemUnit($entity);
        $unit->setId($unitArray['id'] ?? uuid_create());

        // Reconstruct adjustments if necessary here
        $entity->addUnit($unit);
    }
}
```

{% hint style="success" %}
These reconstructed objects reside only in memory and are not persisted unless reattached to the UoW.
{% endhint %}

### 2.3. Metadata Configuration Overrides

When the unit model is flattened into JSON, the related Doctrine mappings still exist but need to be **neutralized** to avoid unintended ORM behavior.

This is achieved using a custom Doctrine **`loadClassMetadata`** event listener which dynamically overrides mapping configuration at runtime.

**✅ Purpose**

Doctrine still loads metadata for associations like:

* `OrderItemUnit → Adjustment`
* `OrderItem → OrderItemUnit`
* `Shipment → OrderItemUnit`

…but since these entities are no longer managed via the ORM lifecycle, their cascade and orphan removal rules might be deactivated.

**📦 Sample: `OrderItemMetadata::loadClassMetadata()`**

```php
public function loadClassMetadata(LoadClassMetadataEventArgs $args): void {
    $meta = $args->getClassMetadata();

    if ($meta->getName() === OrderItemUnit::class) {
        $meta->associationMappings['adjustments']['orphanRemoval'] = false;
        $meta->setAssociationOverride('adjustments', [
            'cascade' => ['persist'],
        ]);
        $meta->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
    }

    if ($meta->getName() === Adjustment::class) {
        $meta->setAssociationOverride('orderItemUnit', [
            'cascade' => [],
        ]);
    }

    if ($meta->getName() === Shipment::class) {
        $meta->associationMappings['units']['orphanRemoval'] = false;
        $meta->setAssociationOverride('units', [
            'cascade' => [],
        ]);
    }

    if ($meta->getName() === OrderItem::class) {
        $meta->associationMappings['units']['orphanRemoval'] = false;
        $meta->setAssociationOverride('units', [
            'cascade' => [],
        ]);
    }
}
```

**🔍 What This Does**

| Entity          | Action Taken                                                      |
| --------------- | ----------------------------------------------------------------- |
| `OrderItemUnit` | Prevents `orphanRemoval` of `adjustments`; disables ID generation |
| `Adjustment`    | Removes cascade to `OrderItemUnit`                                |
| `Shipment`      | Disables cascade + orphan removal on `units`                      |
| `OrderItem`     | Prevents unit cascade and orphan removal                          |

**🧠 Why It's Important**

Without this override:

* Doctrine might attempt to cascade operations (persist/remove) to units or adjustments.
* Flushes could fail due to missing IDs or unwanted inserts.
* The ORM would treat unit-related relations as “live” even though we're persisting through JSON.

By overriding metadata:

* Doctrine behaves safely when encountering unit-related mappings.
* Flush behavior becomes predictable and decoupled from legacy entity structure.

***

### 4. Performance Gains

Before the changes:

<figure><img src="../.gitbook/assets/image (3) (4).png" alt=""><figcaption></figcaption></figure>

After the changes:

<figure><img src="../.gitbook/assets/image (77).png" alt=""><figcaption></figcaption></figure>

Difference:

<figure><img src="../.gitbook/assets/image (2) (4).png" alt=""><figcaption></figcaption></figure>

***

### Summary

By combining **JSON storage** with a **Doctrine UoW-driven detachment strategy**, you keep all unit data intact, but **remove it from the hot path** of most queries. This achieves:

* **Single-query order fetches**
* **No JOINs on units**
* **Minimal entity hydration**
* **On-demand rehydration** for business logic
