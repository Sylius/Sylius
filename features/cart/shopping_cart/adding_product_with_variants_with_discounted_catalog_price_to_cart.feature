@shopping_cart
Feature: Adding a product with selected variant with discounted catalog price to the cart
    In order to select products with proper price
    As a Visitor
    I want to be able to add products with selected variants to cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And the product "T-Shirt" has a "PHP T-Shirt" variant priced at "$20.00"
        And the product "T-Shirt" has a "Kotlin T-Shirt" variant priced at "$400.00"
        And the store has a "Keyboard" configurable product
        And the product "Keyboard" has a "RGB Keyboard" variant priced at "$40.00"
        And the product "Keyboard" has a "Pink Keyboard" variant priced at "$40.00"
        And there is a catalog promotion "Winter sale" that reduces price by "25%" and applies on "PHP T-Shirt" variant

    @ui @api
    Scenario: Adding multiple product variants with discounted price by catalog promotion catalog to the cart
        When I add "PHP T-Shirt" variant of product "T-Shirt" to the cart
        And I add "RGB Keyboard" variant of product "Keyboard" to the cart
        And I check details of my cart
        Then I should see "T-Shirt" with unit price "$15.00" in my cart
        And I should see "Keyboard" with unit price "$40.00" in my cart
