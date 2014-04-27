@localization
Feature: Managing locales
    In order to reach customers from different countries
    As a store owner
    I want to be able to configure locales

    Background:
        Given I am logged in as administrator
          And there are following locales configured:
            | code  | currency | activated |
            | de_DE | EUR      | yes       |
            | en_US | EUR      | yes       |
            | fr_FR | EUR      | yes       |

    Scenario: Seeing index of all locales
        Given I am on the dashboard page
         When I follow "Locales"
         Then I should be on the locale index page
          And I should see 3 locales in the list

    Scenario: Seeing empty index of locales
        Given there are no locales
          And I am on the locale index page
         Then I should see "There are no locales configured"

    Scenario: Accessing the locale adding form
        Given I am on the dashboard page
         When I follow "Locales"
          And I follow "Create locale"
         Then I should be on the locale creation page

    Scenario: Submitting invalid form without code & currency
        Given I am on the locale creation page
         When I press "Create"
         Then I should still be on the locale creation page
          And I should see "Please enter locale code"
          And I should see "Please enter currency"

    Scenario: Creating new locale
        Given I am on the locale creation page
         When I fill in "Code" with "pl_PL"
          And I fill in "Currency" with "PLN"
          And I press "Create"
         Then I should be on the locale index page
          And I should see "Locale has been successfully created."

    Scenario: Locales have to be unique
        Given I am on the locale creation page
         When I fill in "Code" with "de_DE"
          And I fill in "Currency" with "EUR"
          And I press "Create"
         Then I should still be on the locale creation page
          And I should see "This locale already exists."

    Scenario: Locale can contain few currencies
        Given I am on the locale creation page
         When I fill in "Code" with "de_DE"
          And I fill in "Currency" with "USD"
          And I press "Create"
         Then I should be on the locale index page
          And I should see "Locale has been successfully created."

    Scenario: Accessing the editing form from the list
        Given I am on the locale index page
         When I click "edit" near "de_DE"
         Then I should be editing locale with code "de_DE"

    Scenario: Updating the locale
        Given I am editing locale with code "en_US"
         When I fill in "Code" with "en_GB"
          And I press "Save changes"
         Then I should be on the locale index page
          And I should see "Locale has been successfully updated."

    @javascript
    Scenario: Deleting locale from list
        Given I am on the locale index page
         When I click "delete" near "de_DE"
          And I click "delete" from the confirmation modal
         Then I should be on the locale index page
          And I should see "Locale has been successfully deleted."
          And I should not see locale with name "de_DE" in that list
