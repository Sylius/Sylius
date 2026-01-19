# API - Filters Structure Refactor

* Status: Accepted
* Date: 2024-10-22

In effect from version: 2.0

Related PRs: #17290

## Context and Problem Statement

In previous versions, Sylius had API filters grouped by field type rather than by resource and section. This led to filters being applied inconsistently, making it hard to customize them per resource. The filters were also located in the `Filter/Doctrine/` directory, which didn't align with how we organize other Doctrine-related features, creating confusion.
To improve maintainability, clarity, and ease of customization, a refactor is required to group filters per resource and section (admin and shop) and move the custom ones into the `Doctrine/ORM/Filter/` directory, as other Doctrine-related features are organized.

## Decision Drivers

- Need for a clearer structure to reflect resource-based filtering.
- Separation between admin and shop filters.
- Alignment with Doctrine's structure for better maintainability and understanding.

## Considered Options

### Leave Filters Grouped by Field Type

* **Good, because:**
    * No need for refactoring.
* **Bad, because:**
    * Hard to see which filter applies to which resource.
    * Filters applied inconsistently.
    * Difficult to customize.

### Split Filters by Resource and Section

* **Good, because:**
    * Clear separation between resources.
    * Easier to apply and customize filters for admin or shop sections.
* **Bad, because:**
    * Requires refactoring.

### Use a Common Directory for Shared Filters

* **Good, because:**
    * Reduces duplication of similar filters.
* **Bad, because:**
    * Still doesn't provide clear separation between resources and sections.

## Decision Outcome

Chosen option: **Split Filters by Resource and Section**, because it provides the most clarity and maintainability. This approach aligns with the goal of separating filters for admin and shop sections and allows for better customization per resource.
Filters will no longer be organized by field but by resource, ensuring they are clear and easy to maintain.

New structure:

```
/Doctrine/ORM/Filter
    CustomFilter.php
```

The following format will be used for filter IDs: `sylius_api.[filter_type].[context].[resource]`.

Examples:
```
sylius_api.search_filter.admin.[resource]
sylius_api.order_filter.shop.[resource]
sylius_api.exists_filter.admin.[resource]
sylius_api.custom_filter.shop.[resource]
```
