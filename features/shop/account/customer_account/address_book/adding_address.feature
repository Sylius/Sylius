@address_book
Feature: Adding a new address to the book
    In order to have saved addresses on my account
    As a Customer
    I want to be able to add a new address to address book

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer

    @ui @api
    Scenario: Adding address to address book
        When I want to add a new address to my address book
        And I specify the address as "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas"
        And I add it
        Then I should be notified that the address has been successfully added
        And address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" should be marked as my default address
