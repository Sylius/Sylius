@legacy @currency
Feature: Currency management
    In order to receive payments in different currencies
    As a store owner
    I want to configure currencies and exchange rates

    Background:
        Given store has default configuration
        And there are following currencies configured:
            | code | exchange rate | enabled |
            | USD  | 0.76496       | yes     |
            | GBP  | 1.16998       | no      |
            | EUR  | 1.00000       | yes     |
            | AUD  | 0.73986       | yes     |
        And I am logged in as administrator

    Scenario: Browsing all configured currencies
        Given I am on the dashboard page
        When I follow "Currencies"
        Then I should be on the currency index page
        And I should see 4 currencies in the list
        And I should see currency with exchange rate "0.73986" in the list

    Scenario: Seeing empty index of currencies
        Given there are no currencies
        When I am on the currency index page
        Then I should see "There are no currencies to display"

    Scenario: Accessing the currency creation form
        Given I am on the dashboard page
        When I follow "Currencies"
        And I follow "Create currency"
        Then I should be on the currency creation page

    Scenario: Submitting the form without the exchange rate fails
        Given I am on the currency creation page
        When I press "Create"
        Then I should still be on the currency creation page
        And I should see "Please enter exchange rate"

    Scenario: Creating new currency
        Given I am on the currency creation page
        When I select "Polish Zloty" from "Name"
        And I fill in "Exchange rate" with "0.235654"
        And I press "Create"
        Then I should be on the currency index page
        And I should see "currency has been successfully created"

    Scenario: Accessing the currency edit form
        Given I am on the currency index page
        When I click "Edit" near "US Dollar"
        Then I should be editing currency with code "USD"

    Scenario: Updating the currency exchange rate
        Given I am editing currency with code "USD"
        And I fill in "Exchange rate" with "0.76498"
        And I press "Save changes"
        Then I should be on the currency index page
        And I should see currency with exchange rate "0.76498" in the list

    Scenario: Enabling currency
        Given there is a disabled currency "VEF"
        And I am on the currency index page
        When I click "Enable" near "VEF"
        Then I should see enabled currency with code "VEF" in the list
        And I should see "Currency has been successfully enabled"

    Scenario: Disabling currency
        Given there is an enabled currency "VEF"
        And I am on the currency index page
        When I click "Disable" near "VEF"
        Then I should see disabled currency with code "VEF" in the list
        And I should see "Currency has been successfully disabled"

    Scenario: Cannot update currency code
        When I am editing currency with code "USD"
        Then the code field should be disabled

    Scenario: Trying to create a currency with existing code
        Given I am on the currency creation page
        When I select "British Pound" from "Name"
        And I fill in "Exchange rate" with "0.235654"
        And I press "Create"
        Then I should still be on the currency creation page
        And I should see "Currency code must be unique"
