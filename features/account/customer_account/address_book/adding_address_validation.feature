@address_book
Feature: Seeing validation messages during address addition
    In order to be sure that the address I'm trying to add is correct
    As a Customer
    I want to be prevented from adding invalid addresses

    Background:
        Given the store operates on a single channel in "United States"
        And the store also has country "Australia"
        And this country has the "Queensland" province with "AU-QLD" code
        And I am a logged in customer

    @ui
    Scenario: Seeing validation errors when adding an empty address
        When I want to add a new address to my address book
        And I leave every field empty
        And I add it
        Then I should still be on the address addition page
        And I should be notified about 6 errors

    @ui @javascript
    Scenario: The province needs to be selected when the chosen country has at least one stated
        When I want to add a new address to my address book
        And I specify the address as "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas"
        And I choose "Australia" as my country
        And I add it
        Then I should still be on the address addition page
        And I should be notified that the province needs to be specified
