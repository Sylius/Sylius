@shopping_cart
Feature: Clearing cart after logging out
    In order to not allow to use my cart by anybody
    As a Customer
    I want to be able to have my cart cleared after logging out

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Stark T-Shirt" priced at "$12.00"

    @ui @no-api
    Scenario: Clearing cart after logging out
        Given I am a logged in customer
        And I have product "Stark T-Shirt" in the cart
        When I log out
        And I see the summary of my cart
        Then my cart should be empty

    @api @no-ui
    Scenario: Clearing cart after logging out
        Given I am a logged in customer
        And I have product "Stark T-Shirt" in the cart
        When I log out
        Then I should not have access to the summary of my previous cart

    @api @no-ui
    Scenario: Blocking access to cart if logged user did any action over it (what can be treated as signing it)
        Given there is a user "john@snow.com"
        When I add this product to the cart
        And I log in as "john@snow.com" with "sylius" password
        And I add this product to the cart
        And I log out
        Then I should not have access to the summary of my previous cart
