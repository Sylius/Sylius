@taxonomies
Feature: taxonomies
    In order to categorize my merchandise
    As a store owner
    I want to be able to manage taxonomies

    Background:
        Given I am logged in as administrator
          And there are following taxonomies defined:
            | name     |
            | Category |
            | Brand    |
          And taxonomy "Category" has following taxons:
            | Electronics > PC         |
            | Electronics > Mac > iMac |
            | Electronics > Mac > MBP  |
            | Clothing > Gloves        |
            | Clothing > Hats          |

    Scenario: Seeing index of all taxonomies
        Given I am on the dashboard page
         When I follow "Categorization"
         Then I should be on the taxonomy index page
          And I should see 2 taxonomies in the list

    Scenario: Seeing empty index of taxonomies
        Given there are no taxonomies
         When I go to the taxonomy index page
         Then I should see "There are no taxonomies defined"

    Scenario: Accessing the taxonomy creation form
        Given I am on the dashboard page
         When I follow "Categorization"
          And I follow "Create taxonomy"
         Then I should be on the taxonomy creation page

    Scenario: Submitting form without specifying the name
        Given I am on the taxonomy creation page
         When I press "Create"
         Then I should still be on the taxonomy creation page
          And I should see "Please enter taxonomy name"

    Scenario: Creating new taxonomy
        Given I am on the taxonomy creation page
         When I fill in "Name" with "Vendor"
          And I press "Create"
         Then I should be on the page of taxonomy "Vendor"
          And I should see "Taxonomy has been successfully created."

    Scenario: Created taxonomies appear in the list
        Given I created taxonomy "Food"
         When I go to the taxonomy index page
         Then I should see 3 taxonomies in the list
          And I should see taxonomy with name "Food" in that list

    Scenario: Accessing the editing form from the list
        Given I am on the taxonomy index page
         When I click "edit" near "Category"
         Then I should be editing taxonomy "Category"

    Scenario: Updating the taxonomy
        Given I am editing taxonomy "Brand"
         When I fill in "Name" with "Brands"
          And I press "Save changes"
         Then I should be on the page of taxonomy "Brands"
          And I should see "Taxonomy has been successfully updated."

    @javascript
    Scenario: Deleting taxonomy
        Given I am on the taxonomy index page
         When I click "delete" near "Brand"
          And I click "delete" from the confirmation modal
         Then I should be on the taxonomy index page
          And I should see "Taxonomy has been successfully deleted."

    @javascript
    Scenario: Deleted taxonomy disappears from the list
        Given I am on the taxonomy index page
         When I click "delete" near "Category"
          And I click "delete" from the confirmation modal
         Then I should be on the taxonomy index page
          And I should not see taxonomy with name "Category" in that list

    Scenario: Accessing taxonomy tree via the list
        Given I am on the taxonomy index page
         When I click "Category"
         Then I should be on the page of taxonomy "Category"

    Scenario: Seeing index of all taxonomy taxons
        Given I am on the taxonomy index page
         When I click "Category"
         Then I should be on the page of taxonomy "Category"
          And I should see 8 taxons in the list

    Scenario: Creating new taxon under given taxonomy
        Given I am on the page of taxonomy "Category"
          And I follow "Create taxon"
         When I fill in "Name" with "Cars"
          And I press "Create"
         Then I should be on the page of taxonomy "Category"
          And I should see "Taxon has been successfully created."

    Scenario: Creating new taxon with parent
        Given I am on the page of taxonomy "Category"
          And I follow "Create taxon"
         When I fill in "Name" with "iPods"
          And I select "Electronics" from "Parent"
          And I press "Create"
         Then I should be on the page of taxonomy "Category"
          And I should see "Taxon has been successfully created."

    Scenario: Renaming the taxon
        Given I am on the page of taxonomy "Category"
          And I click "edit" near "Clothing"
         When I fill in "Name" with "Clothing and accessories"
          And I press "Save changes"
         Then I should be on the page of taxonomy "Category"
          And I should see "Taxon has been successfully updated."

    Scenario: Deleting taxons
        Given I am on the page of taxonomy "Category"
         When I click "delete" near "Electronics"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should still be on the page of taxonomy "Category"
          And I should see "Taxon has been successfully deleted."

    @javascript
    Scenario: Deleting taxons with js modal
        Given I am on the page of taxonomy "Category"
         When I click "delete" near "Electronics"
          And I click "delete" from the confirmation modal
         Then I should still be on the page of taxonomy "Category"
          And I should see "Taxon has been successfully deleted."

    Scenario: Deleted taxons disappear from the list
        Given I am on the page of taxonomy "Category"
         When I click "delete" near "Clothing"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should still be on the page of taxonomy "Category"
          And "Taxon has been successfully deleted." should appear on the page
          And I should see 5 taxons in the list

    @javascript
    Scenario: Deleted taxons disappear from the list with js modal
        Given I am on the page of taxonomy "Category"
         When I click "delete" near "Clothing"
          And I click "delete" from the confirmation modal
         Then I should still be on the page of taxonomy "Category"
          And "Taxon has been successfully deleted." should appear on the page
          And I should see 5 taxons in the list
