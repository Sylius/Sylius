@managing_countries
Feature: Adding countries with post codes
    In order to sell my goods to different countries and post codes
    As an Administrator
    I want to add a new country with its post codes to the store

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a country with a post code
        When I want to add a new country
        And I choose "United Kingdom"
        And I add the post code "0011" named "post code test"
        And I add it
        Then I should be notified that it has been successfully created
        And the country "United Kingdom" should appear in the store
        And the country "United Kingdom" should have the "post code test" post code zone

    @ui @javascript
    Scenario: Adding a country with two post codes
        When I want to add a new country
        And I choose "United Kingdom"
        And I add the post code "0011" named "post code test"
        And I add the post code "1411" named "abcs"
        And I add it
        Then I should be notified that it has been successfully created
        And the country "United Kingdom" should appear in the store
        And the country "United Kingdom" should have the "post code test" post code zone
        And the country "United Kingdom" should have the "abcs" post code zone