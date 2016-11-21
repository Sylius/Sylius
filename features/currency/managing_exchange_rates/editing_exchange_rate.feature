@managing_exchange_rates
Feature: Editing exchange rate
    In order to modify an exchange rate's configuration
    As an Administrator
    I want to be able to edit an exchange rate

    Background:
        Given the store has currency "US Dollar" and "British Pound"
        And I am logged in as an administrator

    @ui
    Scenario: Change exchange rate's ratio
        Given the store has an exchange rate 1.30 with base currency "US Dollar" and counter currency "British Pound"
        And I am editing this exchange rate
        When I change ratio to 3.21
        And I save my changes
        Then I should be notified that it has been successfully edited
        And it should have a ratio of 3.21

    @ui
    Scenario: Being unable to change currencies
        Given the store has an exchange rate 1.30 with base currency "US Dollar" and counter currency "British Pound"
        When I want to edit this exchange rate
        Then I should see that the base currency is disabled
        And I should see that the counter currency is disabled
