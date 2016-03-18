@legacy @locale
Feature: Managing locales
    In order to reach customers from different countries
    As a store owner
    I want to be able to configure locales

    Background:
        Given store has default configuration
        And there are following locales configured:
            | code  | name                    | enabled |
            | de_DE | German (Germany)        | yes     |
            | en_US | English (United States) | no      |
            | fr_FR | French (France)         | yes     |
        And I am logged in as administrator

    Scenario: Seeing index of all locales
        Given I am on the dashboard page
        When I follow "Locales"
        Then I should be on the locale index page
        And I should see 3 locales in the list

    Scenario: Seeing empty index of locales
        Given there are no locales
        When I am on the locale index page
        Then I should see "There are no locales configured"

    Scenario: Accessing the locale adding form
        Given I am on the dashboard page
        When I follow "Locales"
        And I follow "Create locale"
        Then I should be on the locale creation page

    Scenario: Creating new locale
        Given I am on the locale creation page
        When I select "Polish (Poland)" from "Name"
        And I press "Create"
        Then I should be on the locale index page
        And I should see "Locale has been successfully created"

    Scenario: Listing only available locales during creating a new locale
        When I am on the locale creation page
        Then I should not see name "German (Germany)" as available choice
        And I should not see name "English (United States)" as available choice

    Scenario: Enabling locale
        Given there is a disabled locale "Polish (Poland)"
        And I am on the locale index page
        When I click "Enable" near "Polish (Poland)"
        Then I should see enabled locale with name "Polish (Poland)" in the list
        And I should see "Locale has been successfully enabled"

    Scenario: Disabling locale
        Given there is an enabled locale "Polish (Poland)"
        And I am on the locale index page
        When I click "Disable" near "Polish (Poland)"
        Then I should see disabled locale with name "Polish (Poland)" in the list
        And I should see "Locale has been successfully disabled"
