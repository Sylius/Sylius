@address_book
Feature: Preventing a potential XSS attack during updating the address
    In order to keep my information safe
    As a Customer
    I want to be protected against the potential XSS attacks

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" in my address book
        And this address has province '<img """><script>alert("XSS")</script>">'

    @ui @javascript @no-api
    Scenario: Preventing a potential XSS attack during updating the address
        When I want to edit the address of "Lucifer Morningstar"
        Then I should be able to update it without unexpected alert
