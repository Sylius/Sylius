@products
Feature: Product options
    In order to offer my products in many different variations
    As a store owner
    I want to be able to manage options

    Background:
        Given I am logged in as administrator
          And there are following options:
            | name          | presentation | values           |
            | T-Shirt color | Color        | Red, Blue, Green |
            | T-Shirt size  | Size         | S, M, L          |

    Scenario: Seeing index of all options
        Given I am on the dashboard page
         When I follow "Manage product options"
         Then I should be on the option index page
          And I should see 2 options in the list

    Scenario: Option names are listed
        Given I am on the dashboard page
         When I follow "Manage product options"
         Then I should be on the option index page
          And I should see option with name "T-Shirt color" in the list

    Scenario: Option presentation is displayed in the list
        Given I am on the dashboard page
         When I follow "Manage product options"
         Then I should be on the option index page
          And I should see option with presentation "Color" in the list

    Scenario: Seeing empty index of options
        Given there are no options
         When I go to the option index page
         Then I should see "There are no options configured"

    Scenario: Accessing the option creation form
        Given I am on the dashboard page
         When I follow "Manage product options"
          And I follow "Create option"
         Then I should be on the option creation page

    Scenario: Submitting form without specifying the name
        Given I am on the option creation page
         When I press "Create"
         Then I should still be on the option creation page
          And I should see "Please enter option name."

    Scenario: Submitting form without specifying the presentation
        Given I am on the option creation page
         When I fill in "Internal name" with "Bag color"
          And I press "Create"
         Then I should still be on the option creation page
          And I should see "Please enter option presentation."

    Scenario: Trying to create option without at least 2 values
        Given I am on the option creation page
         When I fill in "Internal name" with "Bag color"
          And I fill in "Presentation" with "Color"
          And I press "Create"
         Then I should still be on the option creation page
          And I should see "Please add at least 2 option values."

    @javascript
    Scenario: Creating option with 4 possible values
        Given I am on the option creation page
         When I fill in "Internal name" with "Bag color"
          And I fill in "Presentation" with "Color"
          And I add following option values:
            | Black  |
            | White  |
            | Brown  |
            | Purple |
          And I press "Create"
         Then I should be on the option index page
          And I should see "Option has been successfully created."

    @javascript
    Scenario: Values are listed after creating the option
        Given I am on the option creation page
         When I fill in "Internal name" with "Mug type"
          And I fill in "Presentation" with "Type"
          And I add following option values:
            | Normal mug  |
            | Large mug   |
            | MONSTER mug |
          And I press "Create"
         Then I should be on the option index page
          And I should see option with value "Normal mug" in that list

    @javascript
    Scenario: Adding values to existing option
        Given I am editing option "T-Shirt size"
          And I add following option values:
            | XL  |
            | XXL |
          And I press "Save changes"
         Then I should be on the option index page
          And "Option has been successfully updated." should appear on the page
          And I should see option with value "XXL" in the list

    Scenario: Created options appear in the list
        Given I created option "Hat size" with values "S, M, L"
         When I go to the option index page
         Then I should see 3 options in the list
          And I should see option with name "Hat size" in that list

    Scenario: Accessing the editing form from the list
        Given I am on the option index page
         When I click "edit" near "T-Shirt color"
         Then I should be editing option "T-Shirt color"

    Scenario: Updating the option
        Given I am editing option "T-Shirt size"
         When I fill in "Internal name" with "T-Shirt sex"
          And I press "Save changes"
         Then I should be on the option index page
          And I should see "Option has been successfully updated."

    Scenario: Deleted option disappears from the list
        Given I am on the option index page
         When I click "delete" near "T-Shirt color"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the option index page
          And I should not see option with name "T-Shirt color" in that list

    @javascript
    Scenario: Deleted option disappears from the list with js modal
        Given I am on the option index page
         When I click "delete" near "T-Shirt color"
          And I click "delete" from the confirmation modal
         Then I should be on the option index page
          And I should not see option with name "T-Shirt color" in that list
