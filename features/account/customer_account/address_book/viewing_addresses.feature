@customer_account
Feature: Viewing a customer's addresses
    In order to see all my addresses in address book
    As a Customer
    I want to be able to browse my addresses

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States" in address book

    @ui
    Scenario: Viewing all addresses
        When I browse my addresses
        Then I should see a single address in a list
