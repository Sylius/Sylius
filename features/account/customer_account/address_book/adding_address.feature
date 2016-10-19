@customer_account
Feature: Adding a customer's address
    In order to have saved addresses on my account
    As a Customer
    I want to be able to add new address to address book

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "John Doe" identified by an email "doe@example.com" and a password "banana"
        And this customer has an address "John Doe", "Banana Street", "90232", "New York", "United States" in address book
        And I am a logged in customer

    @ui
    Scenario: Adding address to address book
        Given I want to add a new address to my address book
        When I specify its data as "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States"
        And I add it
        Then I should be notified that it has been successfully added
        And the address assigned to "Lucifer Morningstar" should appear on the list
