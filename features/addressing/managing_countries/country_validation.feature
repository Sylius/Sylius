@managing_countries
Feature: Country validation
    In order to avoid making mistakes when managing countries
    As an Administrator
    I want to be prevented from adding a new country with invalid code

    Background:
        Given the store operates in "Norway"
        And I am logged in as an administrator

    @ui @api
    Scenario: Trying to add a new country with used code
        When I want to add a new country
        Then I should not be able to choose "Norway"

    @api @no-ui
    Scenario: Trying to add a new country with invalid code
        When I want to add a new country
        And I specify the country code as "NJ"
        And I try to save my changes
        Then I should be notified that the country code is invalid

    @api @no-ui
    Scenario: Trying to add a new country with alpha-3 code
        When I want to add a new country
        And I specify the country code as "USA"
        And I try to save my changes
        Then I should be notified that the country code is invalid

    @api @no-ui
    Scenario: Trying to add a new country with no code
        When I want to add a new country
        And I do not specify the country code
        And I try to save my changes
        Then I should be notified that the country code is required
