@customer_account
Feature: Viewing customer's addresses
    In order to see all my addresses in address book
    As a Customer
    I want to be able to browse my addresses

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer

    @ui
    Scenario: Viewing all addresses
        Given I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States" in address book
        When I browse my addresses
        Then I should see a single address in the list

    @ui
    Scenario: Seeing only my addresses
        Given there is a customer "John Doe" identified by an email "doe@example.com" and a password "banana"
        And this customer has an address "John Doe", "Banana Street", "90232", "New York", "United States" in address book
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States" in address book
        When I browse my addresses
        Then I should see a single address in the list
        And this address should be assigned to "Lucifer Morningstar"
        And I should not see an address assigned to "John Doe"

    @ui
    Scenario: Viewing empty address book
        When I browse my addresses
        Then I should see information about no existing addresses
