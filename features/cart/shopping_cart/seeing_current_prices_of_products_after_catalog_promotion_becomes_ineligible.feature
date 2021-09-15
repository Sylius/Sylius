@shopping_cart
Feature: Seeing current prices of products after catalog promotion becomes ineligible
    In order to buy products in its correct prices
    As a Visitor
    I want to have products with its current prices in the cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt"
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "United States" channel
        And there is a catalog promotion "Winter sale" available in "United States" channel that reduces price by "25%" and applies on "PHP T-Shirt" variant

    @todo
    Scenario: Seeing current price of product in the cart
        When I add "PHP T-Shirt" variant of product "T-Shirt" to the cart
        And I check details of my cart
        Then I should see "T-Shirt" with unit price "$15.00" in my cart
        And I should see "T-Shirt" with original price "$20.00" in my cart

    @todo
    Scenario: Seeing current price of product after catalog promotion becomes ineligible
        When I add "PHP T-Shirt" variant of product "T-Shirt" to the cart
        And the administrator makes this catalog promotion unavailable in the "United States" channel
        And I check details of my cart
        Then I should see "T-Shirt" with price "$20.00" without applied cart promotion in my cart
