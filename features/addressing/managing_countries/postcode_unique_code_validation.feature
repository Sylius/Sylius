@managing_countries
Feature: Post code unique code validation
    In order to uniquely identify provinces
    As an Administrator
    I want to be prevented from adding two postcodes with the same value

    Background:
        Given the store has country "United Kingdom"
        And this country has the postcode "123" named "test"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Trying to add a new postcode duplicate value
        When I want to edit this country
        And I add the postcode "123" named "hello"
        And I try to save changes
        Then I should be notified that the postcode must be unique
        And province with name "Scotland" should not be added in this country
