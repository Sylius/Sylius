# API - State Processors Directory Structure

* Status: Accepted
* Date: 2024-07-15

In effect from version: 2.0

Related PRs: #16565

## Context and Problem Statement

Until version 2.0, Sylius used API Platform version 2.x, 
which utilized `DataPersisters` to handle data persistence 
operations like saving, updating, and deleting resources. 
These `DataPersisters` were all located within a single 
directory, lacking clear organization.

With the upgrade to Sylius 2.0, the API Platform has been 
updated to version 3. One significant change in API Platform 3
is the shift from `DataPersisters` to `StateProcessors`. 
This necessitates a reconsideration of how we organize 
and structure our data-processing classes to ensure clarity, 
maintainability, and scalability.

## Decision Drivers

* Migrate from DataPersisters to StateProcessors
* Clear separation between admin and shop processors
* Clear separation between resources
* Separation of Persist and Remove processors
* Ease of customization in end applications

## Considered Options

### Leave all StateProcessors in one directory

* **Good, because:**
    * Less to refactor
* **Bad, because:**
    * Hard to see which processor is for admin and which for shop
    * No separation of Persist and Remove processors
    * Difficult to customize in end applications

### Separate StateProcessors by resource

* **Good, because:**
    * Clear separation between resources
* **Bad, because:**
    * Need to refactor all processors
    * Hard to see which processor is for admin and which for shop
    * No separation of Persist and Remove processors

### Separate StateProcessors by sections and resources

* **Good, because:**
    * Clear separation between admin and shop processors
    * Clear separation between resources
* **Bad, because:**
    * Need to refactor all processors
    * No separation of Persist and Remove processors

### Separate StateProcessors by sections, resources, and operations

* **Good, because:**
    * Clear separation between admin and shop processors
    * Clear separation between resources
    * Separation of Persist and Remove processors
    * Easy to customize in end applications
* **Bad, because:**
    * Need to refactor all processors

## Decision Outcome

Chosen option: Separate StateProcessors by sections, resources, and operations, 
because it provides a clear and manageable way to customize processors for 
different contexts, including separating persist and remove operations.

New Structure:

```
/StateProcessors
    /Admin
        /AdminUser
            PersistProcessor.php
            RemoveProcessor.php
        /Product
            RemoveProcessor.php
    /Shop
        /Address
            PersistProcessor.php
```
