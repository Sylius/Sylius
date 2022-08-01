@shopping_cart
Feature: Viewing a total quantity of the cart
    In order to easily determine the number of products I'm about to buy
    As a Visitor
    I want to track the total quantity of the cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store has a product "Sylius T-Shirt"
        And the store has a product "Symfony T-Shirt"

    @ui @no-api
    Scenario: Viewing a total quantity for 1 product with quantity equals 1
        When I add product "PHP T-Shirt" to the cart
        Then I should see cart total quantity is 1

    @ui @no-api
    Scenario: Viewing a total quantity for 1 product with quantity equals 2
        When I add 2 products "PHP T-Shirt" to the cart
        Then I should see cart total quantity is 2

    @ui @no-api
    Scenario: Viewing a total quantity for 3 products with various quantities
        When I add 2 products "PHP T-Shirt" to the cart
        And I add 3 products "Sylius T-Shirt" to the cart
        And I add product "Symfony T-Shirt" to the cart
        Then I should see cart total quantity is 6
