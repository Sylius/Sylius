@managing_currencies
Feature: Currency validation
    In order to avoid making mistakes when managing a currency
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Trying to add a new currency without specifying its exchange rate
        Given I want to add a new currency
        When I choose "Euro"
        And I try to add it
        Then I should be notified that exchange rate is required
        And the currency "Euro" should not be added

    @ui
    Scenario: Trying to remove exchange rate from existing currency
        Given the store has currency "Euro" with exchange rate 0.75
        And I want to edit this currency
        When I remove its exchange rate
        And I try to save my changes
        Then I should be notified that exchange rate is required
        And this currency should still have exchange rate equal to 0.75
