@managing_product_variants
Feature: Deleting multiple product variants
    In order to remove test, obsolete or incorrect product variants
    As an Administrator
    I want to be able to delete multiple product variants from the product catalog

    Background:
        Given the store has a product "PHP Mug"
        And this product has "Small PHP Mug", "Medium PHP Mug" and "Big PHP Mug" variants
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple product variants
        When I browse variants of this product
        And I check the "Small PHP Mug" product variant
        And I check also the "Medium PHP Mug" product variant
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single product variant in the list
        And I should see the product variant "Big PHP Mug" in the list
