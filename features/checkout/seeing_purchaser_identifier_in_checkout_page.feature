@checkout
Feature: Seeing purchaser identifier in checkout page
    In order to improve checkout experience
    As a customer
    I want to see my name or email in checkout header

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Gaming chair" priced at "$399.00"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And there is a customer "John Doe" identified by an email "john@example.com" and a password "secret"

    @ui @no-api
    Scenario: Seeing email in checkout header as a guest
        Given I have product "Gaming chair" in the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        Then I should be making an order as "john@example.com"

    @ui @no-api
    Scenario: Seeing full name in checkout header as a logged user with full name
        Given I am a logged in customer with name "John Doe"
        And I have product "Gaming chair" in the cart
        When I complete addressing step with "United States" based billing address
        Then I should be making an order as "John Doe"

    @ui @no-api
    Scenario: Seeing email in checkout header as a logged user without full name
        Given there is a customer account "nameless@example.com"
        And I am logged in as "nameless@example.com"
        And I have product "Gaming chair" in the cart
        When I complete addressing step with "United States" based billing address
        Then I should be making an order as "nameless@example.com"
