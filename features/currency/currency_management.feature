@currencies
Feature: Currency management
    In order to receive payments in different currencies
    As a store owner
    I want to configure currencies and exchange rates

    Background:
        Given I am logged in as administrator
          And there are following currencies configured:
            | currency | exchange rate | enabled |
            | USD      | 0.76496       | yes     |
            | GBP      | 1.16998       | no      |
            | PLN      | 1.00000       | yes     |
            | AUD      | 0.73986       | yes     |

    Scenario: Browsing all configured currencies
        Given I am on the dashboard page
         When I follow "Currencies"
         Then I should be on the currency index page
          And I should see 4 currencies in the list

    Scenario: Exchange rates are visible in the grid
        Given I am on the dashboard page
         When I follow "Currencies"
         Then I should be on the currency index page
          And I should see currency with rate "0.73986" in the list

    Scenario: Seeing empty index of currencies
        Given there are no currencies
         When I am on the currency index page
         Then I should see "There are no currencies to display."

    Scenario: Accessing the currency creation form
        Given I am on the dashboard page
         When I follow "Currencies"
          And I follow "Create currency"
         Then I should be on the currency creation page

    Scenario: Submitting the form without the exchange rate fails
        Given I am on the currency creation page
         When I press "Create"
         Then I should still be on the currency creation page
          And I should see "Please enter exchange rate."

    Scenario: Creating new currency
        Given I am on the currency creation page
         When I select "Polish Zloty" from "Currency"
          And I fill in "Exchange rate" with "0.235654"
          And I press "Create"
         Then I should be on the currency index page
          And I should see "currency has been successfully created."

    Scenario: Created currencies appear in the list
        Given I have configured currency "PLN"
         When I go to the currency index page
         Then I should see 4 currencies in the list
          And I should see a currency with name "Polish Zloty" in that list

    Scenario: Accessing the currency edit form
        Given I am on the currency index page
         When I click "edit" near "US Dollar"
         Then I should be editing currency with code "USD"

    Scenario: Updating the currency exchange rate
        Given I am editing currency with code "USD"
          And I fill in "Exchange rate" with "0.76498"
          And I press "Save changes"
         Then I should be on the currency index page
          And I should see a currenct with exchange rate "0.76498"

    @javascript
    Scenario: Deleting a currency
        Given I am on the currency index page
         When I press "delete" near "US Dollar"
          And I confirm the deletion action
         Then I should still be on the currency index page
          And I should see "Currency has been successfully deleted."
          And I should not see currency with name "US Dollar" in the list
