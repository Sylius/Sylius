@shopping_cart
Feature: Adding a simple product to the cart
    In order to select products for purchase
    As a Visitor
    I want to be able to add simple products to cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Sylius T-Shirt"
        And the store ships everywhere for free

    @api @ui @javascript
    Scenario: Adding a simple product to the cart
        When I add product "Sylius T-Shirt" to the cart
        Then I should be on my cart summary page
        And I should be notified that the product has been successfully added
        And there should be one item in my cart
        And this item should have name "Sylius T-Shirt"
