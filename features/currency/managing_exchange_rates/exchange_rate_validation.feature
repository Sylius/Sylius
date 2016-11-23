@managing_exchange_rates
Feature: Exchange rate validation
    In order to avoid making mistakes when managing exchange rates
    As an Administrator
    I want to be prevented from adding exchange rates without specifying required fields

    Background:
        Given the store has currency "US Dollar" and "British Pound"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new exchange rate without ratio
        Given I want to add a new exchange rate
        When I choose "US Dollar" as the source currency
        And I choose "British Pound" as the target currency
        And I don't specify its ratio
        And I try to add it
        Then I should be notified that ratio is required
        And the exchange rate between "US Dollar" and "British Pound" should not be added

    @ui
    Scenario: Trying to add a new exchange rate with negative ratio
        Given I want to add a new exchange rate
        When I choose "US Dollar" as the source currency
        And I choose "British Pound" as the target currency
        And I specify its ratio as -1.2
        And I try to add it
        Then I should be notified that the ratio must be greater than zero
        And the exchange rate between "US Dollar" and "British Pound" should not be added

    @ui
    Scenario: Trying to add a new exchange rate with same target currency as source
        Given I want to add a new exchange rate
        When I specify its ratio as 1.23
        And I choose "US Dollar" as the source currency
        And I choose "US Dollar" as the target currency
        And I try to add it
        Then I should be notified that source and target currencies must differ
        And the exchange rate between "US Dollar" and "US Dollar" should not be added
