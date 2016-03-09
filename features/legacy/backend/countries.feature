@legacy @addressing
Feature: Countries and provinces
    In order to create tax and shipping zones
    As a store owner
    I want to be able to manage countries and their provinces

    Background:
        Given store has default configuration
        And there are following countries:
            | name    | provinces                       |
            | France  | Lyon, Toulouse, Rennes, Nancy   |
            | China   |                                 |
            | Ukraine | Kiev, Odessa, Cherkasy, Kharkiv |
        And I am logged in as administrator

    Scenario: Seeing index of all countries
        Given I am on the dashboard page
        When I follow "Countries"
        Then I should be on the country index page
        And I should see 3 countries in the list

    Scenario: Names are listed in the index
        Given I am on the dashboard page
        When I follow "Countries"
        Then I should be on the country index page
        And I should see country with name "China" in the list

    Scenario: Country codes are listed in the index
        Given I am on the dashboard page
        When I follow "Countries"
        Then I should be on the country index page
        And I should see country with code "FR" in the list

    Scenario: Seeing empty index of countries
        Given there are no countries
        When I am on the country index page
        Then I should see "There are no countries configured"

    Scenario: Accessing the country creation form
        Given I am on the dashboard page
        When I follow "Countries"
        And I follow "Create country"
        Then I should be on the country creation page

    Scenario: Creating new country
        Given I am on the country creation page
        When I select "Poland" from "Name"
        And I press "Create"
        Then I should be on the page of country "Poland"
        And I should see "Country has been successfully created"

    Scenario: Listing only available countries during creating a new country
        Given there is a disabled country "Germany"
        When I am on the country creation page
        Then I should not see name "France" as available choice
        And I should not see name "Germany" as available choice

    @javascript
    Scenario: Creating new country with provinces
        Given I am on the country creation page
        When I select "Poland" from "Name"
        And I click "Add province"
        And I click "Add province"
        And I fill in the 1st province with "Lubusz"
        And I fill in the 2nd province with "Łódź"
        And I press "Create"
        Then I should see "Country has been successfully created"
        And "Łódź" should appear on the page
        And "Lubusz" should appear on the page

    @javascript
    Scenario: Editing country with duplicated province
        Given I am editing country "France"
        When I click "Add province"
        And I fill in the 5th province with "Lyon"
        And I press "Save changes"
        Then I should see "Province name must be unique"
        And I should see "Province code must be unique"

    Scenario: Created countries appear in the list
        Given I created country "Poland"
        When I go to the country index page
        Then I should see 4 countries in the list
        And I should see country with name "Poland" in that list

    Scenario: Accessing the country editing form
        Given I am on the page of country "France"
        When I follow "Edit"
        Then I should be editing country "France"

    Scenario: Accessing the editing form from the list
        Given I am on the country index page
        When I click "Edit" near "China"
        Then I should be editing country "China"

    Scenario: Cannot edit country code
        When I am editing country "China"
        Then the code field should be disabled

    Scenario: Accessing country details via the list
        Given I am on the country index page
        When I click "China"
        Then I should be on the page of country "China"

    Scenario: Provinces are listed on country page
        Given I am on the country index page
        When I click "France"
        Then I should be on the page of country "France"
        And I should see 4 provinces in the list

    @javascript
    Scenario: Deleting province
        Given I am editing country "France"
        When I remove the first province
        And I press "Save changes"
        Then I should be on the page of country "France"
        And "Nancy" should not appear on the page

    Scenario: Enabling country
        Given there is a disabled country "Poland"
        And I am on the country index page
        When I click "Enable" near "Poland"
        Then I should see enabled country with name "Poland" in the list
        And I should see "Country has been successfully enabled"

    Scenario: Disabling country
        Given there is an enabled country "Poland"
        And I am on the country index page
        When I click "Disable" near "Poland"
        Then I should see disabled country with name "Poland" in the list
        And I should see "Country has been successfully disabled"
