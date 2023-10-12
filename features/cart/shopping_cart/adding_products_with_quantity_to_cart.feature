@shopping_cart
Feature: Adding a simple product of given quantity to the cart
    In order to buy multiple items at once
    As a Visitor
    I want to be able to add a simple product with stated quantity to the cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a wholesale product "T-Shirt grape" priced at "$56.12"
        And the store has a product "T-Shirt banana" priced at "$12.54"

    @ui @api
    Scenario: Adding a product with stated quantity to the cart
        Given there are 10 units of product "T-Shirt banana" available in the inventory
        When I add 5 of them to my cart
        Then I should be on my cart summary page
        And I should be notified that the product has been successfully added
        And I should see "T-Shirt banana" with quantity 5 in my cart

    @ui @api
    Scenario: Adding way too many products sets their quantity to 9999
        Given there are 100000 units of product "T-Shirt banana" available in the inventory
        When I add 20000 of them to my cart
        Then I should be on my cart summary page
        And I should be notified that the product has been successfully added
        And I should see "T-Shirt banana" with quantity 9999 in my cart

    @ui @api
    Scenario: Adding a wholesale product to the cart
        Given there are 10 units of product "T-Shirt grape" available in the inventory
        When I add 10 products "T-Shirt grape" to the cart
        Then I should be on my cart summary page
        And I should be notified that the product has been successfully added
        And there should be one item in my cart
        And this item should have name "T-Shirt grape"
