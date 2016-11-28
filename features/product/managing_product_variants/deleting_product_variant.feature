@managing_product_variants
Feature: Deleting a product variant
    In order to remove test, obsolete or incorrect product variants
    As an Administrator
    I want to be able to delete product variant from the product catalog

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP Mug"
        And the product "PHP Mug" has "Medium PHP Mug" variant priced at "$40.00"
        And I am logged in as an administrator

    @domain @ui
    Scenario: Deleted variant disappears from the product catalog
        When I delete the "Medium PHP Mug" variant of product "PHP Mug"
        Then I should be notified that it has been successfully deleted
        And this variant should not exist in the product catalog
