@viewing_cart
Feature: Adding a product with selected option to the cart
    In order to select products for purchase
    As a Visitor
    I want to be able to add products with selected options to cart

    Background:
        Given the store operates on a single channel in "France"

    @ui
    Scenario: Adding a product with multiple options to the cart
        Given the store has a product "T-shirt banana"
        And this product has option "Size" with value "S" priced at "€12.35"
        When I add this product with Size "S" to the cart
        Then I should be on my cart summary page
        And I should be notified that the product has been successfully added
        And I should see one item on my product list
        And this item should have name "T-shirt banana"
        And this product should have size "S"
