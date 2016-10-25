@address_book
Feature: Making changes in existing addresses
    In order to keep my address information up to date
    As a Customer
    I want to be able to edit an address

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" in my address book

    @ui
    Scenario: Changing the names on my address
        Given I am editing the address of "Lucifer Morningstar"
        When I change my first name to "Stephanie"
        And I change my last name to "Edgley"
        And I save my changed address
        Then I should be notified that it has been successfully updated
        And I should still see a single address in my book
        And this address should be assigned to "Stephanie Edgley"

    @ui
    Scenario: Changing my location
        Given I am editing the address of "Lucifer Morningstar"
        When I change my street to "Vildegard Av"
        And I change my city to "Liverpool"
        And I change my postcode to "GBA-20B"
        And I save my changed address
        Then I should be notified that it has been successfully updated
        And I should still see a single address in my book
        And it should contain "Vildegard Av"
        And it should contain "Liverpool"
        And it should contain "GBA-20B"

    @ui @javascript
    Scenario: Changing province to one from the list
        Given the store also has country "Australia"
        And this country has the "Queensland" province with "AU-QLD" code
        And I am editing the address of "Lucifer Morningstar"
        When I choose "Australia" as my country
        And I choose "Queensland" as my province
        And I save my changed address
        Then I should be notified that it has been successfully updated
        And I should still see a single address in my book
        And it should contain "Australia"
        And it should contain "Queensland"

    @ui @javascript
    Scenario: Changing province to for country with no provinces defined
        Given I am editing the address of "Lucifer Morningstar"
        When I specify "New York" as my province
        And I save my changed address
        Then I should be notified that it has been successfully updated
        And I should still see a single address in my book
        And it should contain "New York"
