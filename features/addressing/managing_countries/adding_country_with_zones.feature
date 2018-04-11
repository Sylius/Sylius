@managing_countries
Feature: Adding countries with postcodes
    In order to sell my goods to different countries and postcodes
    As an Administrator
    I want to add a new country with its postcodes to the store

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a country with a postcode
        When I want to add a new country
        And I choose "United Kingdom"
        And I add the postcode "0011" named "postcode test"
        And I add it
        Then I should be notified that it has been successfully created
        And the country "United Kingdom" should appear in the store
        And the country "United Kingdom" should have the "postcode test" postcode zone

    @ui @javascript
    Scenario: Adding a country with two postcodes
        When I want to add a new country
        And I choose "United Kingdom"
        And I add the postcode "0011" named "postcode test"
        And I add the postcode "1411" named "abcs"
        And I add it
        Then I should be notified that it has been successfully created
        And the country "United Kingdom" should appear in the store
        And the country "United Kingdom" should have the "postcode test" postcode zone
        And the country "United Kingdom" should have the "abcs" postcode zone
