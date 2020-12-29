@checkout
Feature: Seeing proper checkout name when im checking out with existing account.

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Gaming chair" priced at "$399"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a customer "John Doe" identified by an email "john@example.com" and a password "secret"

    @ui
    Scenario: Seeing email in checkout header as a guest
        Given I have product "Gaming chair" in the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        Then I should see "john@example.com" in checkout header

    @ui
    Scenario: Seeing full name in checkout header as a logged user with full name
        Given I am a logged in customer with name "John Doe"
        And I have product "Gaming chair" in the cart
        When I complete addressing step with "United States" based billing address
        Then I should see "John Doe" in checkout header

    @ui
    Scenario: Seeing email in checkout header as a logged user without full name
        Given there is a customer account "nameless@example.com"
        And I am logged in as "nameless@example.com"
        And I have product "Gaming chair" in the cart
        When I complete addressing step with "United States" based billing address
        Then I should see "nameless@example.com" in checkout header
