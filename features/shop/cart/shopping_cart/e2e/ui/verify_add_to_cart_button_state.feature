@shopping_cart
Feature: Verify Add-to-Cart Button State Validation
    In order to prevent ordering more items than available in inventory
    As a Visitor
    I want to see that the "Add to cart" button is disabled when the requested quantity exceeds available stock

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Sylius T-Shirt"
        And "Sylius T-Shirt" product is tracked by the inventory
        And there are 2 units of product "Sylius T-Shirt" available in the inventory

    @no-api @ui @javascript
    Scenario: "Add to cart" button is disabled when requested quantity exceeds stock
        When I view product "Sylius T-Shirt"
        And I update the quantity of this product to 3
        Then I should not be able to add it
