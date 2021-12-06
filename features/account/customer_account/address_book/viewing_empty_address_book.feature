@address_book
Feature: Viewing empty address book
    In order to see only added addresses
    As a Customer
    I want to be able to see empty address book

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer with name "Lucifer Morningstar"

    @ui @api
    Scenario: Viewing an empty address book
        When I browse my address book
        Then there should be no addresses
