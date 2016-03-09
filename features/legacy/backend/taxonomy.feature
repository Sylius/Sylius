@legacy @taxonomy
Feature: taxonomy
    In order to categorize my merchandise
    As a store owner
    I want to be able to manage taxons

    Background:
        Given store has default configuration
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
            | RTX2 | Brand    |
        And taxon "Category" has following children:
            | Electronics[TX1] > Featured[TX3]        |
            | Electronics[TX1] > Mac[TX4] > iMac[TX5] |
            | Electronics[TX1] > Mac[TX4] > MBP[TX6]  |
            | Clothing[TX2] > Gloves[TX7]             |
            | Clothing[TX2] > Hats[TX8]               |
        And I am logged in as administrator

    Scenario: Seeing index of all taxons
        Given I am on the dashboard page
        When I follow "Categorization"
        Then I should be on the taxon index page
        And I should see 10 taxons in the list

    Scenario: Seeing empty index of taxons
        Given there are no taxons
        When I go to the taxon index page
        Then I should see "There are no taxons to display"

    Scenario: Accessing the taxon creation form
        Given I am on the dashboard page
        When I follow "Categorization"
        And I follow "Create taxon"
        Then I should be on the taxon creation page

    Scenario: Submitting form without specifying the name
        Given I am on the taxon creation page
        When I press "Create"
        Then I should still be on the taxon creation page
        And I should see "Please enter taxon name"

    Scenario: Creating new taxon
        Given I am on the taxon creation page
        When I fill in "Name" with "Vendor"
        And I fill in "Code" with "RTX3"
        And I press "Create"
        Then I should be on the taxon index page
        And I should see "Taxon has been successfully created."

    Scenario: Created taxons appear in the list
        Given I created taxon "Food" with code "RTX4"
        When I go to the taxon index page
        Then I should see 11 taxons in the list
        And I should see taxon with name "Food" in that list

    Scenario: Accessing the editing form from the list
        Given I am on the taxon index page
        When I click "Edit" near "Category"
        Then I should be editing taxon "Category"

    Scenario: Updating the taxon
        Given I am editing taxon "Brand"
        When I fill in "Name" with "Brands"
        And I press "Save changes"
        Then I should be on the taxon index page
        And I should see "Taxon has been successfully updated."

    @javascript
    Scenario: Deleting taxon
        Given I am on the taxon index page
        When I click "Delete" near "Brand"
        And I click "Delete" from the confirmation modal
        Then I should be on the taxon index page
        And I should see "Taxon has been successfully deleted."

    @javascript
    Scenario: Deleted taxon disappears from the list
        Given I am on the taxon index page
        When I click "Delete" near "Electronics"
        And I click "Delete" from the confirmation modal
        Then I should be on the taxon index page
        And I should not see taxon with name "Electronics" in that list

    Scenario: Accessing taxon tree via the list
        Given I am on the taxon index page
        When I click "Category"
        Then I should be on the page of taxon "Category"

    Scenario: Seeing index of a taxons children
        Given I am on the taxon index page
        When I click "Category"
        Then I should be on the page of taxon "Category"
        And I should see 8 taxons in the list

    Scenario: Creating new taxon with parent
        Given I am on the page of taxon "Category"
        And I follow "Create taxon"
        When I fill in "Name" with "iPods"
        And I fill in "Code" with "TR9"
        And I select "Electronics" from "Parent"
        And I press "Create"
        Then I should be on the taxon index page
        And I should see "Taxon has been successfully created."

    Scenario: Renaming the taxon
        Given I am on the page of taxon "Category"
        And I click "Edit" near "Clothing"
        When I fill in "Name" with "Clothing and accessories"
        And I press "Save changes"
        Then I should be on the taxon index page
        And I should see "Taxon has been successfully updated."

    @javascript
    Scenario: Deleting taxons
        Given I am on the page of taxon "Category"
        When I click "Delete" near "Electronics"
        And I click "Delete" from the confirmation modal
        Then I should be on the taxon index page
        And I should see "Taxon has been successfully deleted."

    @javascript
    Scenario: Deleted taxons disappear from the list
        Given I am on the taxon index page
        When I click "Delete" near "Clothing"
        And I click "Delete" from the confirmation modal
        Then I should be on the taxon index page
        And "Taxon has been successfully deleted." should appear on the page
        And I should see 7 taxons in the list

    Scenario: Cannot update taxon code
        When I am editing taxon "Electronics"
        Then the code field should be disabled

    Scenario: Try create taxon with existing code
        Given I am on the page of taxon "Category"
        And I follow "Create taxon"
        When I fill in "Name" with "Cars"
        And I fill in "Code" with "TX1"
        And I press "Create"
        Then I should still be on the taxon creation page
        And I should see "Taxon with given code already exists."

    Scenario: Try create taxon without code
        Given I am on the page of taxon "Category"
        And I follow "Create taxon"
        When I fill in "Name" with "Cars"
        And I press "Create"
        Then I should still be on the taxon creation page
        And I should see "Please enter taxon code."
