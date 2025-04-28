@checkout
Feature: Preventing payment step completion without a selected method
    In order to be prevented from finishing payment step without selecting a method
    As a Visitor
    I want to be prevented from finishing the payment step with no method selected

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store has a product "PHP T-Shirt" priced at "$19.99"

    @api @no-ui
    Scenario: Preventing payment step completion if there are no available methods
        Given I added product "PHP T-Shirt" to the cart
        When I addressed the cart to "United States"
        And I proceed with selecting "Free" shipping method
        And I check the details of my cart
        Then I should see that no payment method is assigned
        And there should not be any payment methods available for selection

    @no-api @ui
    Scenario: Preventing payment step completion if there are no available methods
        Given I added product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        When I specify the email as "jon.snow@example.com"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I proceed with selecting "Free" shipping method
        Then I should not be able to complete the payment step
        And there should be information about no payment methods available for my order

    @api @no-ui
    Scenario: Preventing payment step completion if there are no available methods for a channel
        Given the store has "Cash on Delivery" payment method not assigned to any channel
        And I have product "PHP T-Shirt" in the cart
        And I addressed the cart to "United States"
        And I proceed with selecting "Free" shipping method
        When I check the details of my cart
        Then I should see that no payment method is assigned
        And there should not be any payment methods available for selection

    @no-api @ui
    Scenario: Preventing payment step completion if there are no available methods for a channel
        Given the store has "Cash on Delivery" payment method not assigned to any channel
        And I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the email as "jon.snow@example.com"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I proceed with selecting "Free" shipping method
        And I do not select any payment method
        Then I should not be able to complete the payment step
        And there should be information about no payment methods available for my order

    @api @no-ui
    Scenario: Preventing payment step completion if a payment method is disabled
        Given the store has a payment method "Offline" with a code "Offline"
        And this payment method is disabled
        And I have product "PHP T-Shirt" in the cart
        And I addressed the cart
        And I proceed with selecting "Free" shipping method
        When I check the details of my cart
        Then I should see that no payment method is assigned
        And there should not be any payment methods available for selection

    @no-api @ui
    Scenario: Preventing payment step completion if a payment method is disabled
        Given the store has a payment method "Offline" with a code "Offline"
        And this payment method is disabled
        And I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the email as "jon.snow@example.com"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I proceed with selecting "Free" shipping method
        And I do not select any payment method
        Then I should not be able to complete the payment step
        And there should be information about no payment methods available for my order

    @api @no-ui
    Scenario: Preventing payment step completion if a payment method is disabled or not assigned to a channel
        Given the store has a payment method "Offline" with a code "Offline"
        And this payment method is disabled
        And the store has "Cash on Delivery" payment method not assigned to any channel
        And I have product "PHP T-Shirt" in the cart
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I proceed with selecting "Free" shipping method
        When I check the details of my cart
        Then I should see that no payment method is assigned
        And there should not be any payment methods available for selection

    @no-api @ui
    Scenario: Preventing payment step completion if a payment method is disabled or not assigned to a channel
        Given the store has a payment method "Offline" with a code "Offline"
        And this payment method is disabled
        And the store has "Cash on Delivery" payment method not assigned to any channel
        And I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the email as "jon.snow@example.com"
        And I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I proceed with selecting "Free" shipping method
        And I do not select any payment method
        Then I should not be able to complete the payment step
        And there should be information about no payment methods available for my order
