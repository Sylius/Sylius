@address_book
Feature: Preventing not logged user from operations on the address book
    In order to protect address book from unauthorised operation
    As a Visitor
    I want not to be able to operate on address book

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "John Doe" identified by an email "doe@example.com" and a password "banana"
        And this customer has an address "John Doe", "Banana Street", "90232", "New York", "United States", "Kansas" in their address book

    @api
    Scenario: Trying to add new address as a Visitor
        When I want to add a new address to my address book
        And I specify the address as "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas"
        And I try to add it
        Then I should not be able to add it

    @api
    Scenario: Trying to view address as a Visitor
        When I try to view details of address belongs to "John Doe"
        Then I should not see any details of address

    @api
    Scenario: Trying to delete address as a Visitor
        When I try to delete address belongs to "John Doe"
        Then I should not be able to delete it
