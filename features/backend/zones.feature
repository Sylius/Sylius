@addressing
Feature: Zones
    As a store owner
    I want to be able to manage zones
    In order to apply taxes and allow shipping to geographical areas

    Background:
        Given I am logged in as administrator
          And there are following zones:
            | name                      | type     | members                                       |
            | Baltic states             | country  | Lithuania, Latvia, Estonia                    |
            | USA GMT-8                 | province | Washington, Oregon, Nevada, Idaho, California |
            | Baltic states + USA GMT-8 | zone     | Baltic states, USA GMT-8                      |
            | Germany                   | country  | Germany                                       |

    Scenario: Seeing index of all zones
        Given I am on the dashboard page
         When I follow "Zones"
         Then I should be on the zone index page
          And I should see 4 zones in the list

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
          And I should see "Please enter zone name."

    Scenario: Creating new zone requires adding at least 1 member
        Given I am on the zone creation page
          And I fill in "Name" with "EU"
         When I press "Create"
         Then I should still be on the zone creation page
          And I should see "Please add at least 1 zone member."

    @javascript
    Scenario: Creating new zone built from countries
        Given I am on the zone creation page
          And I fill in "Name" with "EU"
          And I select "Country" from "Type"
          And I click "Add member"
          And I select "Estonia" from "Country"
         When I press "Create"
         Then I should be on the page of zone "EU"
          And I should see "Zone has been successfully created."
          And "Estonia" should appear on the page

    Scenario: Created zones appear in the list
        Given I created zone "EU"
          And I go to the zone index page
         Then I should see 5 zones in the list
          And I should see zone with name "EU" in that list

    Scenario: Accessing the zone edit form
        Given I am on the page of zone "USA GMT-8"
         When I follow "edit"
         Then I should be editing zone "USA GMT-8"

    Scenario: Accessing the editing form from list
        Given I am on the zone index page
         When I click "edit" near "USA GMT-8"
         Then I should be editing zone "USA GMT-8"

    Scenario: Updating the zone
        Given I am editing zone "USA GMT-8"
         When I fill in "Name" with "USA GMT-9"
          And I press "Save changes"
         Then I should be on the page of zone "USA GMT-9"
          And I should see "Zone has been successfully updated."
          But I should not see zone with name "USA GMT-8" in the list

    @javascript
    Scenario: Adding zone member to the existing zone
        Given I am editing zone "Baltic states"
         When I click "Add member"
          And I select "Estonia" from "Country"
          And I press "Save changes"
         Then I should be on the page of zone "Baltic states"
          And I should see "Zone has been successfully updated."
          And "Estonia" should appear on the page

    Scenario: Deleting zone
        Given I am on the page of zone "USA GMT-8"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the zone index page
          And I should see "Zone has been successfully deleted."

    @javascript
    Scenario: Deleting zone with js modal
        Given I am on the page of zone "USA GMT-8"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the zone index page
          And I should see "Zone has been successfully deleted."

    Scenario: Deleting zone from list
        Given I am on the zone index page
         When I click "delete" near "USA GMT-8"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should still be on the zone index page
          And I should see "Zone has been successfully deleted."

    @javascript
    Scenario: Deleting zone from list with js modal
        Given I am on the zone index page
         When I click "delete" near "USA GMT-8"
          And I click "delete" from the confirmation modal
         Then I should still be on the zone index page
          And I should see "Zone has been successfully deleted."

    Scenario: Deleted zone disappears from the list
        Given I am on the page of zone "Germany"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the zone index page
          But I should not see zone with name "Germany" in that list

    @javascript
    Scenario: Deleted zone disappears from the list with js modal
        Given I am on the page of zone "Germany"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the zone index page
          But I should not see zone with name "Germany" in that list
