# UPGRADE FROM `2.2` TO `2.3`

## OrderByIdentifierSqlWalker direction matching

The `OrderByIdentifierSqlWalker` now matches the sort direction of the appended identifier column to the
direction of the last existing `ORDER BY` column, instead of always using `ASC`.

This significantly improves database index utilization on sorted queries. Previously, a query like
`ORDER BY number DESC` would produce `ORDER BY number DESC, id ASC`, which prevented the database from
using a single index scan due to mixed sort directions. Now it produces `ORDER BY number DESC, id DESC`,
allowing a backward index scan.

**Behavioral change:** When sorting in descending order, tie-breaking by identifier is now also descending
(e.g., `id DESC` instead of `id ASC`). This means that among rows with equal values in the sorted column,
the row with the **highest** id will appear first, instead of the lowest. This has no practical impact on
pagination stability, which remains fully deterministic.

## Dependencies

1. The `knplabs/gaufrette` and `knplabs/knp-gaufrette-bundle` packages have been removed.

   The Gaufrette integration has been unusable as a filesystem adapter.
   Since Sylius 2.0 the default filesystem adapter uses Flysystem instead. 

   If your application depends on the Gaufrette packages directly, require them explicitly in your `composer.json`.
