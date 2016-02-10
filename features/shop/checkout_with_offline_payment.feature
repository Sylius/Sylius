@ui-checkout
Feature: Checkout with offline payment
    In order to pay with cash or by external means
    As a Customer
    I want to be able to complete checkout process without paying

    Background:
        Given the store is operating on a single channel
        And default currency is "EUR"
        And there is user "john@example.com" identified by "password123"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships to "France"
        And the store ships everything for free
        And the store allows paying offline

    Scenario: Successfully placing an order
        Given I am logged in as "john@example.com"
        And I added product "PHP T-Shirt" to the cart
        When I proceed selecting "Offline" payment method
        And I confirm my order
        Then I should see the thank you page
