@shopping_cart
Feature: Adding a product with selected variant with discounted catalog price to the cart
    In order to select products with proper price
    As a Visitor
    I want to be able to add products with selected variants to cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt"
        And this product has "PHP T-Shirt" variant priced at "$20"
        And there is a catalog promotion "Winter sale" that reduces price by "25%" and applies on "PHP T-Shirt" variant

    @todo
    Scenario: Adding a product with multiple variants with discounted catalog price to the cart
        When I add "PHP T-Shirt" variant of this product to the cart
        Then I should be on my cart summary page
        And I should be notified that the product has been successfully added
        And I should see "T-Shirt Banana" with unit price "$15.00" in my cart
