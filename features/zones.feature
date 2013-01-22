Feature: Zones
    As a store owner
    I want to be able to manage zones
    In order to be able to apply other settings on geographical area

    Background:
        Given I am logged in as administrator
          And there are following zones:
            | name                      | type     | members                                       |
            | Baltic states             | country  | Lithuania, Latvia, Estonia                    |
            | USA GMT-8                 | province | Washington, Oregon, Nevada, Idaho, California |
            | Baltic states + USA GMT-8 | zone     | Baltic states, USA GMT-8                      |

    Scenario: Seeing index of all zones
        Given I am on the dashboard page
         When I follow "Zones"
         Then I should be on the zone index page
          And I should see 3 zones in the list

    Scenario: Seeing empty index of zones
        Given there are no zones
          And I am on the zone index page
         Then I should see "There are no zones configured"

    Scenario: Accessing the zone creation form
        Given I am on the dashboard page
         When I follow "Zones"
          And I follow "Create zone"
         Then I should be on the zone creation page

    Scenario: Submitting invalid form
        Given I am on the zone creation page
         When I press "Create"
         Then I should still be on the zone creation page
          And I should see "Please enter zone name"

    Scenario: Creating new zone
        Given I am on the zone creation page
          And I fill in "Name" with "EU"
          And I select "Country" from "Type"
         When I press "Create"
         Then I should be on the page of zone "EU"
          And I should see "Zone has been successfully created."

    @javascript
    Scenario: Creating new zone with members
        Given I am on the zone creation page
          And I fill in "Name" with "EU"
          And I select "Country" from "Type"
          And I click "Add member"
          And I select "Estonia" from "Country"
         When I press "Create"
         Then I should be on the page of zone "EU"
          And I should see "Zone has been successfully created."
          And I should see "Estonia"

    Scenario: Created zones appear in the list
        Given I created zone "EU"
          And I go to the zone index page
         Then I should see 4 zones in the list
          And I should see zone with name "EU" in that list

    Scenario: Updating the zone
        Given I am on the page of zone "USA GMT-8"
          And I follow "Edit"
         When I fill in "Name" with "USA GMT-9"
          And I press "Save changes"
         Then I should be on the page of zone "USA GMT-9"
          And I should see "Zone has been successfully updated."

    @javascript
    Scenario: Adding zone member to the existing zone
        Given I am on the page of zone "Baltic states"
          And I follow "Edit"
         When I fill in "Name" with "Baltic states 2"
          And I select "Country" from "Type"
          And I click "Add member"
          And I select "Estonia" from "Country"
          And I press "Save changes"
         Then I should be on the page of zone "Baltic states 2"
          And I should see "Zone has been successfully updated."
          And I should see "Baltic states 2"
          And I should see "Estonia"

    Scenario: Deleting zone
        Given I am on the page of zone "USA GMT-8"
         When I click "Delete"
         Then I should be on the zone index page
          And I should see "Zone has been successfully deleted."

    Scenario: Deleted zone disappears from the list
        Given I am on the page of zone "USA GMT-8"
         When I click "Delete"
         Then I should be on the zone index page
          And I should not see zone with name "USA GMT-8" in that list

    Scenario: Displaying the zone details
        Given I am on the page of zone "USA GMT-8"
         Then I should see "USA GMT-8"
          And I should see "Washington"
          And I should see "Oregon"
          And I should see "Nevada"
          And I should see "Idaho"
          And I should see "California"
