@shopping_cart
Feature: Adding a product with selected variant with discounted catalog price to the cart
    In order to select products with proper price
    As a Visitor
    I want to be able to add products with selected variants to cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt"
        And this product has "PHP T-Shirt" variant priced at "$20"
        And the store has a product "Keyboard"
        And this product has "RGB Keyboard" variant priced at "$40"
        And there is a catalog promotion "Winter sale" that reduces price by "25%" and applies on "PHP T-Shirt" variant

    @todo
    Scenario: Adding multiple product variants with discounted price by catalog promotion catalog to the cart
        Given I add product "PHP T-Shirt" to the cart
        And I add product "RGB Keyboard" to the cart
        When I check details of my cart
        Then I should see "PHP T-Shirt" with unit price "$15.00" in my cart
        And I should see "RGB Keyboard" with unit price "$40.00" in my cart
