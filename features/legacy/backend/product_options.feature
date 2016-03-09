@legacy @product
Feature: Product options
    In order to offer my products in many different variations
    As a store owner
    I want to be able to manage options

    Background:
        Given store has default configuration
        And there are following options:
            | code | name          | values                          |
            | O1   | T-Shirt color | Red[OV1], Blue[OV2], Green[OV3] |
            | O2   | T-Shirt size  | S[OV4], M[OV5], L[OV6]          |
        And I am logged in as administrator

    Scenario: Seeing index of all options
        Given I am on the dashboard page
        When I follow "Manage product options"
        Then I should be on the product option index page
        And I should see 2 options in the list
        And I should see option with name "T-Shirt color" in the list

    Scenario: Seeing empty index of options
        Given there are no product options
        When I go to the product option index page
        Then I should see "There are no options configured"

    Scenario: Accessing the option creation form
        Given I am on the dashboard page
        When I follow "Manage product options"
        And I follow "Create option"
        Then I should be on the product option creation page

    Scenario: Submitting form without specifying the name
        Given I am on the product option creation page
        When I press "Create"
        Then I should still be on the product option creation page
        And I should see "Please enter option name"

    Scenario: Trying to create option without at least 2 values
        Given I am on the product option creation page
        When I fill in "Code" with "PO3"
        And I fill in "Name" with "T-Shirt color"
        And I press "Create"
        Then I should still be on the product option creation page
        And I should see "Please add at least 2 option values"

    @javascript
    Scenario: Creating option with 4 possible values
        Given I am on the product option creation page
        When I fill in "Code" with "PO3"
        And I fill in "Name" with "T-Shirt color"
        And I add following option values:
            | OV7  | Black  |
            | OV8  | White  |
            | OV9  | Brown  |
            | OV10 | Purple |
        And I press "Create"
        Then I should be on the product option index page
        And I should see "Option has been successfully created"

    @javascript
    Scenario: Values are listed after creating the option
        Given I am on the product option creation page
        When I fill in "Code" with "PO3"
        And I fill in "Name" with "Type"
        And I add following option values:
            | OV7 | Normal mug  |
            | OV8 | Large mug   |
            | OV9 | MONSTER mug |
        And I press "Create"
        Then I should be on the product option index page
        And I should see option with value containing "Normal mug" in that list

    @javascript
    Scenario: Adding values to existing option
        Given I am editing product option "O2"
        And I add following option values:
            | OV7 | XL  |
            | OV8 | XXL |
        And I press "Save changes"
        Then I should be on the product option index page
        And "Option has been successfully updated" should appear on the page
        And I should see option with value containing "XXL" in the list

    Scenario: Created options appear in the list
        Given I created option "Hat size" with values "S[VO3], M[VO4], L[V05]" and option code "PO3"
        When I go to the product option index page
        Then I should see 3 options in the list
        And I should see option with name "Hat size" in that list

    Scenario: Updating the option
        Given I am on the product option index page
        And I click "Edit" near "T-Shirt color"
        When I fill in "Name" with "T-Shirt sex"
        And I press "Save changes"
        Then I should be on the product option index page
        And I should see "Option has been successfully updated"

    @javascript
    Scenario: Deleted option disappears from the list
        Given I am on the product option index page
        When I click "Delete" near "T-Shirt color"
        And I click "Delete" from the confirmation modal
        Then I should be on the product option index page
        And I should not see option with name "T-Shirt color" in that list

    Scenario: Cannot edit product option code
        When I am editing product option "O2"
        Then the code field should be disabled

    @javascript
    Scenario: Try add product option without code
        Given I am on the product option creation page
        And I fill in "Name" with "Bag color"
        And I add following option values:
            | OV7 | Black |
            | OV8 | White |
        And I press "Create"
        Then I should still be on the product option creation page
        And I should see "Please enter option code"

    @javascript
    Scenario: Try add product option with existing code
        Given I am on the product option creation page
        When I fill in "Code" with "O1"
        And I fill in "Name" with "Bag color"
        And I add following option values:
            | OV7 | Black |
            | OV8 | White |
        And I press "Create"
        Then I should still be on the product option creation page
        And I should see "The option with given code already exists"

    @javascript
    Scenario: Try add product option values without code
        Given I am on the product option creation page
        When I fill in "Code" with "O3"
        And I fill in "Name" with "Bag Color"
        And I add option value "Black"
        And I add option value "White"
        And I press "Create"
        Then I should still be on the product option creation page
        And I should see "Please enter option value code"

    @javascript
    Scenario: Try add product option value with existing code
        Given I am on the product option creation page
        When I fill in "Code" with "O3"
        And I fill in "Name" with "Bag color"
        And I add following option values:
            | OV1 | Black |
            | OV8 | White |
        And I press "Create"
        Then I should still be on the product option creation page
        And I should see "The option value with given code already exists"
