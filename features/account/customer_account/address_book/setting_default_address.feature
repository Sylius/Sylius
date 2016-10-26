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

    @ui
    Scenario: Setting an order as default
        Given I am browsing my address book
        When I set the address of "Lucifer Morningstar" as default
        Then the address of "Lucifer Morningstar" should be my default
        And I should still have 2 addresses in my address book

    @ui
    Scenario: Only one address can be default
        Given I am browsing my address book
        And my default address is of "Lucifer Morningstar"
        When I set the address of "Archangelo Prime" as default
        Then the address of "Archangelo Prime" should be my default
