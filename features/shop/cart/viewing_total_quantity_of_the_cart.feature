@shopping_cart
Feature: Viewing a total quantity of the cart
    In order to easily determine the number of products I'm about to buy
    As a Customer
    I want to track the total quantity of the cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store has a product "Sylius T-Shirt"
        And the store has a product "Symfony T-Shirt"

    @no-api @ui @mink:chromedriver
    Scenario: Viewing a total quantity for 1 product with quantity equals 1
        When I add product "PHP T-Shirt" to the cart
        Then I should see cart total quantity is 1

    @no-api @ui
    Scenario: Viewing a total quantity for 1 product with quantity equals 2
        Given I added 2 products "PHP T-Shirt" to the cart
        When I check details of my cart
        Then I should see cart total quantity is 2

    @no-api @ui
    Scenario: Viewing a total quantity for 3 products with various quantities
        Given I added 2 products "PHP T-Shirt" to the cart
        And I added 3 products "Sylius T-Shirt" to the cart
        And I added product "Symfony T-Shirt" to the cart
        When I check details of my cart
        Then I should see cart total quantity is 6
