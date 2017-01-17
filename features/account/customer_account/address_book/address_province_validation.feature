@address_book
Feature: Province field entry stays after validation errors
    In order to not specify the province each time i make a mistake
    As a Customer
    I want the province which I chose to still be on the form after I make a mistake in the form

    Background:
        Given the store operates on a single channel in "United States"
        And the store also has country "Australia"
        And this country has the "Queensland" province with "AU-QLD" code
        And this country also has the "Victoria" province with "AU-VIC" code
        And I am a logged in customer
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" in my address book
        And I have an address "Fletcher Ren", "Upper Barkly Street", "3377", "Ararat", "Australia", "Victoria" in my address book

    @ui @javascript
    Scenario: The province name stays after validation error
        Given I am editing the address of "Lucifer Morningstar"
        When I remove the street
        And I save my changed address
        Then I should still be on the "Lucifer Morningstar" address edit page
        And I should still have "Arkansas" as my specified province

    @ui @javascript
    Scenario: The selected province stays after validation error
        Given I am editing the address of "Fletcher Ren"
        When I remove the street
        And I save my changed address
        Then I should still be on the "Fletcher Ren" address edit page
        And I should still have "Victoria" as my chosen province
