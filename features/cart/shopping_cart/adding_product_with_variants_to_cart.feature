@shopping_cart
Feature: Adding a product with selected variant to the cart
    In order to select products for purchase
    As a Visitor
    I want to be able to add products with selected variants to cart

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Adding a product with multiple variants to the cart
        Given the store has a product "T-shirt banana"
        And this product has "Small logo" variant priced at "$12.35"
        And this product has "Medium logo" variant priced at "$15.35"
        When I add "Small logo" variant of this product to the cart
        Then I should be on my cart summary page
        And I should be notified that the product has been successfully added
        And there should be one item in my cart
        And this item should have name "T-shirt banana"
        And this item should have variant "Small logo"
        And this item should have code "SMALL_LOGO"
