@homepage
Feature: Viewing a number of items in a cart
    In order to track a number of items I have in my cart
    As a Customer
    I want to easily track a number of items in the cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
    @ui
    Scenario: Viewing a number of items in a cart button for 1 product with quantity equals 1
        When I add product "PHP T-Shirt" to the cart
        Then I should see 1 item in the cart button

    @ui
    Scenario: Viewing a number of items in a cart button for 1 product with quantity equals 2
        When I add 2 products "PHP T-Shirt" to the cart
        Then I should see 2 items in the cart button
