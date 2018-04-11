@managing_countries
Feature: Post code validation
    In order to avoid making mistakes when managing a postcode
    As an Administrator
    I want to be prevented from adding invalid entities

    Background:
        Given the store has country "United Kingdom"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Trying to add a new postcode without specifying its value
        When I want to create a new postcode in country "United Kingdom"
        And I name the postcode "Scotland"
        But I do not specify the postcode value
        And I try to save changes
        Then I should be notified that code should not be blank
        And postcode with name "Scotland" should not be added in this country

    @ui @javascript
    Scenario: Trying to add a new postcode without specifying its name
        When I want to create a new postcode in country "United Kingdom"
        And I specify the postcode value as "123"
        But I do not name the postcode
        And I try to save changes
        Then I should be notified that postcode should have a name
        And province with code "123" should not be added in this country
