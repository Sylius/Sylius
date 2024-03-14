@address_book
Feature: Marking an address as default
    In order to have one of my addresses set as default
    As a Customer
    I want to mark an address as default

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" in my address book
        And I have an address "Archangelo Prime", "Mountain Av", "90640", "Isla del Muerte", "United States" in my address book

    @ui @api
    Scenario: Having no address marked as default at first
        When I browse my address book
        Then I should have 2 addresses in my address book
        And I should not have a default address

    @ui @api
    Scenario: Setting an address as default
        When I browse my address book
        And I set the address of "Lucifer Morningstar" as default
        Then I should be notified that the address has been set as default
        And I should have 2 addresses in my address book
        And address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" should be set as my default address

    @ui @api
    Scenario: Only one address can be default
        Given my default address is of "Lucifer Morningstar"
        When I browse my address book
        And I set the address of "Archangelo Prime" as default
        Then I should be notified that the address has been set as default
        And address "Archangelo Prime", "Mountain Av", "90640", "Isla del Muerte", "United States" should be set as my default address
        And the address assigned to "Lucifer Morningstar" should be in my book
