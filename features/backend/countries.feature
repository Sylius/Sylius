@addressing
Feature: Countries and provinces
    In order to create tax and shipping zones
    As a store owner
    I want to be able to manage countries and their provinces

    Background:
        Given I am logged in as administrator
        And there is default currency configured
        And there are following countries:
            | name    | provinces                       |
            | France  | Lyon, Toulouse, Rennes, Nancy   |
            | China   |                                 |
            | Ukraine | Kiev, Odessa, Cherkasy, Kharkiv |

    Scenario: Seeing index of all countries
        Given I am on the dashboard page
        When I go to the country index page
        Then I should see 3 countries in the list
        And I should see country with name "China" in the list
        And I should see country with iso code "FR" in the list

    Scenario: Seeing empty index of countries
        Given there are no countries
        When I am on the country index page
        Then I should see "There are no countries configured"

    Scenario: Accessing the country creation form
        Given I am on the country index page
        When I follow "Create country"
        Then I should be on the country creation page

    Scenario: Submitting an invalid form
        Given I am on the country creation page
        When I press "Save"
        Then I should still be on the country creation page
        And I should see "Please enter country name."
        And I should see "Please enter country ISO code."

    Scenario: Creating new country
        Given I am on the country creation page
        When I fill in "Name" with "Poland"
        And I fill in "ISO name" with "PL"
        And I press "Save"
        Then I should be editing country "Poland"
        And I should see "Country has been successfully created."

    @javascript
    Scenario: Creating new country with provinces
        Given I am on the country creation page
        When I fill in "Name" with "Poland"
        And I fill in "ISO name" with "PL"
        And I click "Add province"
        And I fill in province name with "Łódź"
        And I press "Save"
        Then I should be editing country "Poland"
        And I should see "Country has been successfully created."
        And "Łódź" should appear on the page

    Scenario: Accessing the editing form from the list
        Given I am on the country index page
        When I click "edit" near "China"
        Then I should be editing country "China"

    Scenario: Updating the country and province
        Given I am editing country "Ukraine"
        When I fill in "Name" with "Russia"
        And I fill in "ISO name" with "RU"
        And I fill in province name with "Volgograd"
        And I press "Save changes"
        Then I should be editing country "Russia"
        And "Russia" should appear on the page

    Scenario: Deleting country via the list
        Given I am on the country index page
        When I press "delete" near "China"
        Then I should still be on the country index page
        And I should see "Country has been successfully deleted."
        But I should not see country with name "China" in the list
