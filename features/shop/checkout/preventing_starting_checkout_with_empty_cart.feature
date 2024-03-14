@checkout
Feature: Preventing starting checkout with an empty cart
    In order to proceed through the checkout correctly
    As a Customer
    I want to be prevented from accessing checkout with an empty cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows paying Offline
        And the store ships everywhere for Free
        And the store has a product "PHP T-Shirt"
        And I am a logged in customer

    @ui @no-api
    Scenario: Being unable to start checkout addressing step with an empty cart
        When I try to open checkout addressing page
        Then I should be redirected to my cart summary page

    @ui @api
    Scenario: Being unable to start checkout shipping step with an empty cart
        Given I added product "PHP T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Joe Doe"
        When I remove product "PHP T-Shirt" from the cart
        Then I should not be able to proceed checkout shipping step
        And I should be redirected to my cart summary page

    @ui @api
    Scenario: Being unable to start checkout payment step with an empty cart
        Given I added product "PHP T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Joe Doe"
        And I completed the shipping step with "Free" shipping method
        When I remove product "PHP T-Shirt" from the cart
        Then I should not be able to proceed checkout payment step
        And I should be redirected to my cart summary page

    @ui @api
    Scenario: Being unable to start checkout complete step with an empty cart
        Given I added product "PHP T-Shirt" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Joe Doe"
        And I completed the shipping step with "Free" shipping method
        And I completed the payment step with "Offline" payment method
        When I remove product "PHP T-Shirt" from the cart
        Then I should not be able to proceed checkout complete step
        And I should be redirected to my cart summary page
