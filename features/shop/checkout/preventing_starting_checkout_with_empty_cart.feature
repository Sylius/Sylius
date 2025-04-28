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

    @no-api @ui
    Scenario: Being unable to start checkout addressing step with an empty cart
        When I try to open checkout addressing page
        Then I should be redirected to my cart summary page

    @api @ui @javascript
    Scenario: Being unable to start checkout shipping step with an empty cart
        Given I added product "PHP T-Shirt" to the cart
        And I addressed the cart
        When I remove product "PHP T-Shirt" from the cart
        Then I should not be able to proceed checkout shipping step
        And I should be redirected to my cart summary page

    @api @ui @javascript
    Scenario: Being unable to start checkout payment step with an empty cart
        Given I added product "PHP T-Shirt" to the cart
        And I addressed the cart
        And I chose "Free" shipping method
        When I remove product "PHP T-Shirt" from the cart
        Then I should not be able to proceed checkout payment step
        And I should be redirected to my cart summary page

    @api @ui @javascript
    Scenario: Being unable to start checkout complete step with an empty cart
        Given I added product "PHP T-Shirt" to the cart
        And I addressed the cart
        And I chose "Free" shipping method
        And I chose "Offline" payment method
        When I remove product "PHP T-Shirt" from the cart
        Then I should not be able to proceed checkout complete step
        And I should be redirected to my cart summary page
