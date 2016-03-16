@checkout
Feature: Checkout with offline payment
    In order to pay with cash or by external means
    As a Customer
    I want to be able to complete checkout process without paying

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline

    @ui
    Scenario: Successfully placing an order
        Given I am logged in customer
        And I have product "PHP T-Shirt" in the cart
        When I proceed selecting "Offline" payment method
        And I confirm my order
        Then I should see the thank you page
