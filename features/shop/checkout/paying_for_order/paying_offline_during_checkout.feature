@paying_for_order
Feature: Paying offline during checkout
    In order to pay with cash or by external means
    As a Customer
    I want to be able to complete checkout process without paying

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline

    @ui @api
    Scenario: Successfully placing an order
        Given I am a logged in customer
        And I have product "PHP T-Shirt" in the cart
        And I have specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I proceeded with "Free" shipping method and "Offline" payment method
        And I confirm my order
        Then I should see the thank you page
