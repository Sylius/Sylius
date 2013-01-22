Feature: Countries and provinces
    As a store owner
    I want to be able to manage countries and provinces

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
          And I am on the country index page
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
          And I should see "Please enter country name"

    Scenario: Creating new country
        Given I am on the country creation page
          And I fill in "Name" with "Poland"
          And I fill in "ISO name" with "PL"
         When I press "Create"
         Then I should be on the page of country "Poland"
          And I should see "Country has been successfully created."

    @javascript
    Scenario: Creating new country with provinces
        Given I am on the country creation page
          And I fill in "Name" with "Poland"
          And I fill in "ISO name" with "PL"
          And I follow "Add province"
          And I fill in province name with "Łódź"
         When I press "Create"
         Then I should be on the page of country "Poland"
          And I should see "Country has been successfully created."
          And I should see "Łódź"

    Scenario: Created countries appear in the list
        Given I created country "Poland"
          And I go to the country index page
         Then I should see 4 countries in the list
          And I should see country with name "Poland" in that list

    Scenario: Updating the country and province
        Given I am on the page of country "Ukraine"
          And I follow "Edit"
         When I fill in "Name" with "Russia"
          And I fill in province name with "Volgograd"
          And I press "Save changes"
         Then I should be on the page of country "Russia"
         And I should see "Volgograd"

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
          And I delete province "Toulouse"
         Then I should be on the page of country "France"
          And I should not see "Toulouse"

    Scenario: Displaying the country details
        Given I am on the page of country "France"
         Then I should see "France"
          And I should see "Lyon"
          And I should see "Toulouse"
          And I should see "Rennes"
          And I should see "Nancy"
