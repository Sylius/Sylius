@addressing
Feature: Countries and provinces
    In order to create tax and shipping zones
    As a store owner
    I want to be able to manage countries and their provinces

    Background:
        Given I am logged in as administrator
          And there are following countries:
            | name    | provinces                       |
            | France  | Lyon, Toulouse, Rennes, Nancy   |
            | China   |                                 |
            | Ukraine | Kiev, Odessa, Cherkasy, Kharkiv |

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

    Scenario: ISO codes are listed in the index
        Given I am on the dashboard page
         When I follow "Countries"
         Then I should be on the country index page
          And I should see country with iso code "FR" in the list

    Scenario: Seeing empty index of countries
        Given there are no countries
         When I am on the country index page
         Then I should see "There are no countries configured"

    Scenario: Accessing the country creation form
        Given I am on the dashboard page
         When I follow "Countries"
          And I follow "Create country"
         Then I should be on the country creation page

    Scenario: Submitting form without name filled
        Given I am on the country creation page
         When I press "Create"
         Then I should still be on the country creation page
          And I should see "Please enter country name."

    Scenario: Country ISO code is required
        Given I am on the country creation page
         When I fill in "Name" with "Poland"
         When I press "Create"
         Then I should still be on the country creation page
          And I should see "Please enter country ISO code."

    Scenario: Creating new country
        Given I am on the country creation page
         When I fill in "Name" with "Poland"
          And I fill in "ISO name" with "PL"
          And I press "Create"
         Then I should be on the page of country "Poland"
          And I should see "Country has been successfully created."

    @javascript
    Scenario: Creating new country with provinces
        Given I am on the country creation page
         When I fill in "Name" with "Poland"
          And I fill in "ISO name" with "PL"
          And I click "Add province"
          And I fill in province name with "Łódź"
          And I press "Create"
         Then I should be on the page of country "Poland"
          And I should see "Country has been successfully created."
          And "Łódź" should appear on the page

    Scenario: Created countries appear in the list
        Given I created country "Poland"
         When I go to the country index page
         Then I should see 4 countries in the list
          And I should see country with name "Poland" in that list

    Scenario: Accessing the country editing form
        Given I am on the page of country "France"
         When I follow "edit"
         Then I should be editing country "France"

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
         Then I should be on the page of country "Russia"
          And "Russia" should appear on the page

    Scenario: Deleting country via the list button
        Given I am on the country index page
         When I press "delete" near "China"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should still be on the country index page
          And I should see "Country has been successfully deleted."
          But I should not see country with name "China" in the list

    @javascript
    Scenario: Deleting country via the list button with js modal
        Given I am on the country index page
         When I press "delete" near "China"
          And I click "delete" from the confirmation modal
         Then I should still be on the country index page
          And I should see "Country has been successfully deleted."
          But I should not see country with name "China" in the list

    Scenario: Deleting country
        Given I am on the page of country "China"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the country index page
          And I should see "Country has been successfully deleted."

    @javascript
    Scenario: Deleting country with js modal
        Given I am on the page of country "China"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the country index page
          And I should see "Country has been successfully deleted."

    Scenario: Deleted country disappears from the list
        Given I am on the page of country "France"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the country index page
          And I should not see country with name "France" in that list

    @javascript
    Scenario: Deleted country disappears from the list with js modal
        Given I am on the page of country "France"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the country index page
          And I should not see country with name "France" in that list

    Scenario: Accessing country details via the list
        Given I am on the country index page
         When I click "China"
         Then I should be on the page of country "China"

    Scenario: Provinces are listed on country page
        Given I am on the country index page
         When I click "France"
         Then I should be on the page of country "France"
          And I should see 4 provinces in the list

    Scenario: Deleting province
        Given I am on the page of country "France"
         When I press "delete" near "Toulouse"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should still be on the page of country "France"
          And "Toulouse" should not appear on the page

    @javascript
    Scenario: Deleting province with js modal
        Given I am on the page of country "France"
         When I press "delete" near "Toulouse"
          And I click "delete" from the confirmation modal
         Then I should still be on the page of country "France"
          And "Toulouse" should not appear on the page
