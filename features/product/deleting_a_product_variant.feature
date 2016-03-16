@product
Feature: Deleting a product variant
    In order to remove test or incorrect product variants
    As an Administrator
    I want to be able to delete product variant from the registry

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP Mug"
        And the product "PHP Mug" has "Medium PHP Mug" variant priced at €40.00

    @todo
    Scenario: Deleted variant disappears from the registry
        When I delete the "Medium PHP Mug" variant of product "PHP Mug"
        Then this variant should not exist in the product catalog
