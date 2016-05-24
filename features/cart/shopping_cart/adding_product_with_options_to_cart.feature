@shopping_cart
Feature: Adding a product with selected option to the cart
    In order to select specific variant of product for purchase
    As a Visitor
    I want to be able to add products with selected options to cart

    Background:
        Given the store operates on a single channel in "France"

    @ui
    Scenario: Adding a product with multiple options to the cart
        Given the store has a product "T-shirt banana"
        And this product has option "Size" with values "S" and "M"
        And this product is available in "S" size priced at "€12.54"
        When I add "T-shirt banana" with Size "S" to the cart
        Then I should be on my cart summary page
        And I should be notified that the product has been successfully added
        And there should be one item in my cart
        And this item should have name "T-shirt banana"
        And this product should have size "S"
