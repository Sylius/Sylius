@legacy @addressing
Feature: Zones
    As a store owner
    I want to be able to manage zones
    In order to apply taxes and allow shipping to geographical areas

    Background:
        Given store has default configuration
        And there are following zones:
            | name                      | type     | members                                       | scope    |
            | Baltic states             | country  | Lithuania, Latvia, Estonia                    | content  |
            | USA GMT-8                 | province | Washington, Oregon, Nevada, Idaho, California | shipping |
            | Baltic states + USA GMT-8 | zone     | Baltic states, USA GMT-8                      |          |
            | Germany                   | country  | Germany                                       | price    |
        And I am logged in as administrator

    Scenario: Seeing index of all zones
        Given I am on the dashboard page
        When I follow "Zones"
        Then I should be on the zone index page
        And I should see 4 zones in the list

    Scenario: Seeing empty index of zones
        Given there are no zones
        And I am on the zone index page
        Then I should see "There are no zones configured"

    @javascript
    Scenario: Accessing the zone creation form
        Given I am on the zone index page
        And I want to create a country zone
        When I follow "Create"
        Then I should be on the zone creation page for type "country"

    Scenario: Submitting invalid form
        Given I am on the zone creation page for type "country"
        When I press "Create"
        Then I should still be on the zone creation page for type "country"
        And I should see "Please enter zone name"
        And I should see "Please enter zone code"

    Scenario: Creating new zone requires adding at least 1 member
        Given I am on the zone creation page for type "country"
        And I fill in "Name" with "European Union"
        And I fill in "Code" with "EU"
        When I press "Create"
        Then I should still be on the zone creation page for type "country"
        And I should see "Please add at least 1 zone member"

    @javascript
    Scenario: Creating new zone built from countries
        Given I am on the zone creation page for type "country"
        And I fill in "Name" with "European Union"
        And I fill in "Code" with "EU"
        And I select "shipping" from "Scope"
        And I add zone member "Estonia"
        And I add zone member "Germany"
        When I press "Create"
        Then I should be on the page of zone "European Union"
        And I should see "Zone has been successfully created"
        And "EE" should appear on the page
        And "DE" should appear on the page
        And "shipping" should appear on the page

    Scenario: Created zones appear in the list
        Given I created zone "EU"
        And I go to the zone index page
        Then I should see 5 zones in the list
        And I should see zone with name "EU" in that list

    Scenario: Accessing the zone edit form
        Given I am on the page of zone "USA GMT-8"
        When I follow "Edit"
        Then I should be editing zone "USA GMT-8"

    Scenario: Accessing the editing form from list
        Given I am on the zone index page
        When I click "Edit" near "USA GMT-8"
        Then I should be editing zone "USA GMT-8"

    @javascript
    Scenario: Updating the zone
        Given I am editing zone "USA GMT-8"
        When I fill in "Name" with "USA GMT-9"
        And I remove the first province
        And I press "Save changes"
        Then I should be on the page of zone "USA GMT-9"
        And I should see "Zone has been successfully updated"
        And "California" should not appear on the page

    Scenario: Cannot edit zone code
        When I am editing zone "USA GMT-8"
        Then the code field should be disabled

    Scenario: Updating the zone
        Given I am editing zone "USA GMT-8"
        When I fill in "Name" with "USA GMT-9"
        And I press "Save changes"
        Then I should be on the page of zone "USA GMT-9"
        And I should see "Zone has been successfully updated"
        But I should not see zone with name "USA GMT-8" in the list

    @javascript
    Scenario: Adding zone member to the existing zone
        Given there is country "Poland"
        And I am editing zone "Baltic states"
        When I add zone member "Poland"
        And I press "Save changes"
        Then I should be on the page of zone "Baltic states"
        And I should see "Zone has been successfully updated"
        And "PL" should appear on the page

    @javascript
    Scenario: Deleting zone
        Given I am on the page of zone "Germany"
        When I press "Delete"
        And I click "Delete" from the confirmation modal
        Then I should be on the zone index page
        And I should see "Zone has been successfully deleted"
        And I should not see zone with name "Germany" in that list

    @javascript
    Scenario: Deleting zone from list
        Given I am on the zone index page
        When I click "Delete" near "USA GMT-8"
        And I click "Delete" from the confirmation modal
        Then I should still be on the zone index page
        And I should see "Zone has been successfully deleted"
        And I should not see zone with name "USA GMT-8" in that list
