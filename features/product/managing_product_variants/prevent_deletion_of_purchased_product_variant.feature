@managing_product_variants
Feature: Prevent deletion of purchased product variant
    In order to maintain proper order history
    As an Administrator
    I want to be prevented from deleting purchased product variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And the store has a product "PHP Mug"
        And the product "PHP Mug" has "Medium PHP Mug" variant priced at "$40.00"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "Medium PHP Mug" variant of product "PHP Mug"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @domain @ui
    Scenario: Purchased product variant cannot be deleted
        When I try to delete the "Medium PHP Mug" variant of product "PHP Mug"
        Then I should be notified that this variant is in use and cannot be deleted
        And this variant should still exist in the product catalog
