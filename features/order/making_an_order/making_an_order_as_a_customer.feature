@making_orders
Feature: Making an order
    In order to know about new orders
    As an Administrator
    I want to be able to manage new orders made by customers

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a user "john.doe@example.com"
        And this user is logged in
        And he has product "PHP T-Shirt" in the cart
        And he proceed selecting "Offline" payment method
        And he confirm his order
        And I am logged in as an administrator

    @ui
    Scenario: Verifying that order has new state
        When I browse orders
        Then I should see a single order from customer "john.doe@example.com"
        And it should have a "new" state
