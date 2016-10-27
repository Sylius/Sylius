@address_book
Feature: Removing an address from my book
    In order to have only relevant addresses in my address book
    As a Customer
    I want to be able to remove an address

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" in my address book

    @ui
    Scenario:
        When I browse my address book
        And I delete the "Lucifer Morningstar" address
        Then I should be notified that the address has been successfully deleted
        And there should be no addresses
