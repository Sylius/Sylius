@inventory_on_product_page
Feature: Displaying inventory tab on simple product edit page
    In order to manage product inventory
    As an Administrator
    I want to see inventory tab only on simple product edit page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP Mug"
        And the store has a "PHP T-Shirt" configurable product
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Seeing inventory on simple product edit page
        When I want to modify the "PHP Mug" product
        Then I should see inventory of this product

    @ui @no-api
    Scenario: Not seeing inventory on configurable product edit page
        When I want to modify the "PHP T-Shirt" product
        Then I should not see inventory of this product
