# API - Query Extensions Directory Structure

* Status: accepted
* Date: 2024-07-16

In effect from version: 2.0

Related PRs: #16573

## Context and Problem Statement

The current directory structure of Query Extensions in the codebase is disorganized and unclear.

```
/Doctrine
    /QueryCollectionExtension
        ExampleExtension.php
    /QueryExtension
    /QueryItemExtension
```

There is no differentiation between sections such as Admin, Shop, and resources, resulting in unrelated extensions being grouped together based solely on their type.

## Decision Drivers

* Improve code organization and clarity
* Clear separation of extensions by section
* Clear separation of extensions by resource

## Considered Options

### Maintain Current Structure

* **Good, because:**
    * No refactoring required
    * Informs about extension type
* **Bad, because:**
    * Everything is in one place with no separation other than extension type
    * Difficult to find related extensions for a specific section
    * Difficult to find related extensions for specific resources
    * Lacks information about the ORM context

### Separate by Section

* **Good, because:**
    * Clear separation by section (Admin, Shop, Common)
    * Improves clarity and maintainability by grouping extensions by role rather than type of service
    * Informs about the ORM context of the extensions
* **Bad, because:**
    * Requires refactoring existing code
    * Lacks further organization by specific resources
    * Lacks further organization by extension type

### Separate by Resource

* **Good, because:**
    * Clear separation by specific resources
    * Improves clarity and maintainability by grouping extensions by role rather than type of service
    * Informs about the ORM context of the extensions
* **Bad, because:**
    * Requires refactoring existing code
    * Lacks further organization by section
    * Lacks further organization by extension type

### Separate by Section and Resource

* **Good, because:**
    * Clear separation by section (Admin, Shop, Common)
    * Further organized by specific resources
    * Improves clarity and maintainability by grouping extensions by role
    * Informs about the ORM context of the extensions
* **Bad, because:**
    * Requires refactoring existing code
    * Lacks further organization by extension type

## Decision Outcome

Chosen option: `Separate by Section and Resource`, because it provides the best balance of clarity and maintainability, ensuring that related extensions are grouped together in a logical manner. 
This structure will improve navigation and future scalability of the codebase.

New Structure:
```
Doctrine/ORM/QueryExtension/
    /Admin
        /Product
            ExampleExtension.php
    /Shop
    /Common
```
