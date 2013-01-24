Feature: Countries and provinces
    As a store owner
    I want to be able to manage countries and provinces
    In order to group them into geographical zones

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

    Scenario: Seeing empty index of countries
        Given there are no countries
         When I am on the country index page
         Then I should see "There are no countries configured"

    Scenario: Accessing the country creation form
        Given I am on the dashboard page
         When I follow "Countries"
          And I follow "Create country"
         Then I should be on the country creation page

    Scenario: Submitting invalid form
        Given I am on the country creation page
         When I press "Create"
         Then I should still be on the country creation page
          And I should see "Please enter country name."

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
          And I follow "Add province"
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
         When I follow "Edit"
         Then I should be editing country "France"

    Scenario: Accessing the editing form from the list
        Given I am on the country index page
         When I click "Edit" near "China"
         Then I should be editing country "China"

    Scenario: Updating the country and province
        Given I am editing country "Ukraine"
         When I fill in "Name" with "Russia"
         When I fill in "ISO name" with "RU"
          And I fill in province name with "Volgograd"
          And I press "Save changes"
         Then I should be on the page of country "Russia"

    Scenario: Deleting country
        Given I am on the page of country "China"
         When I follow "Delete"
         Then I should be on the country index page
          And I should see "Country has been successfully deleted."

    Scenario: Deleted country disappears from the list
        Given I am on the page of country "France"
         When I follow "Delete"
         Then I should be on the country index page
          And I should not see country with name "France" in that list

    Scenario: Deleting province
        Given I am on the page of country "France"
         When I click "Delete" near "Toulouse"
         Then I should be on the page of country "France"
          And "Toulouse" should not appear on the page
