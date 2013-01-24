Feature: Product options
    As a store owner
    I want to be able to manage options
    In order to offer my products in many different variations

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

    Scenario: Seeing empty index of options
        Given there are no options
         When I am on the option index page
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
         When I fill in "Name" with "Bag color"
          And I press "Create"
         Then I should still be on the option creation page
          And I should see "Please enter option presentation."

    Scenario: Trying to create option without at least 2 values
        Given I am on the option creation page
         When I fill in "Name" with "Bag color"
          And I fill in "Presentation" with "Color"
          And I press "Create"
         Then I should still be on the option creation page
          And I should see "Please add at least 2 option values."

    @javascript
    Scenario: Creating option with 4 possible values
        Given I am on the option creation page
         When I fill in "Name" with "Bag color"
          And I fill in "Presentation" with "Color"
          And I add following option values:
            | Black  |
            | White  |
            | Brown  |
            | Purple |
          And I press "Create"
         Then I should be on the page of option "Bag color"
          And I should see "Option has been successfully created."

    @javascript
    Scenario: Values are listed after creating the option
        Given I am on the option creation page
         When I fill in "Name" with "Mug type"
          And I fill in "Presentation" with "Type"
          And I add following option values:
            | Normal mug  |
            | Large mug   |
            | MONSTER mug |
          And I press "Create"
         Then I should be on the page of option "Mug type"
          And I should see option with value "Normal mug" in that list

    Scenario: Created options appear in the list
        Given I created option "Hat size" with values "S, M, L"
         When I go to the option index page
         Then I should see 3 options in the list
          And I should see option with name "Hat size" in that list

    Scenario: Accessing the option editing form
        Given I am on the page of option "T-Shirt size"
         When I follow "Edit"
         Then I should be editing option "T-Shirt size"

    Scenario: Accessing the editing form from the list
        Given I am on the option index page
         When I click "Edit" near "T-Shirt color"
         Then I should be editing option "T-Shirt color"

    Scenario: Updating the option
        Given I am editing option "T-Shirt size"
         When I fill in "Name" with "T-Shirt sex"
          And I press "Save changes"
         Then I should be on the page of option "T-Shirt sex"
          And I should see "Option has been successfully updated."

    Scenario: Deleting option
        Given I am on the page of option "T-Shirt size"
         When I follow "Delete"
         Then I should be on the option index page
          And I should see "Option has been successfully deleted."

    Scenario: Deleted option disappears from the list
        Given I am on the page of option "T-Shirt color"
         When I follow "Delete"
         Then I should be on the option index page
          And I should not see option with name "T-Shirt color" in that list

    Scenario: Accessing the option details page from list
        Given I am on the option index page
         When I click "Details" near "T-Shirt color"
         Then I should be on the page of option "T-Shirt color"
