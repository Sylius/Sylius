@address_book
Feature: Removing an address from my book
    In order to have only relevant addresses in my address book
    As a Customer
    I want to be able to remove an address

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" in my address book
        And my default address is of "Lucifer Morningstar"

    @ui
    Scenario: Viewing address created after placing an order
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the first and last name as "Mike Ross" for billing address
        And I complete the addressing step
        And I proceed with "Free" shipping method and "Offline" payment
        And I confirm my order
        And I browse my address book
        And I delete the "Mike Ross" address
        Then I should be notified that the address has been successfully deleted
        And I should have a single address in my address book
