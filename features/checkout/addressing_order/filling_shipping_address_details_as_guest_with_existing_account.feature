@checkout
Feature: Addressing an order and signing in
    In order to address an order
    As a customer
    I want to be able to sign in and fill addressing details

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for free
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And there is a customer "Francis Underwood" identified by an email "francis@underwood.com" and a password "whitehouse"

    @ui @javascript
    Scenario: Addressing an order and signing in
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the email as "francis@underwood.com"
        And I specify the password as "whitehouse"
        And I sign in
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step
