@legacy @product
Feature: Product archetypes
    In order to create similar products faster
    As a store owner
    I want to be able to create archetypes

    Background:
        Given store has default configuration
        And there are following options:
            | code | name           | values                                            |
            | O1   | T-Shirt color  | Red[OV1], Blue[OV2], Green[OV3]                   |
            | O2   | T-Shirt size   | S[OV4], M[OV5], L[OV6]                            |
            | O3   | Bag color      | Black[OV7], Light balsamic[OV8]                   |
            | O4   | Beverage size  | Tall[OV9], Grande[OV10], Venti[OV11]              |
            | O5   | Beverage milk  | None[OV12], Whole[OV13], Skinny[OV14], Soya[OV15] |
            | O6   | Coffee variety | Colombian[OV16], Ethiopian[OV17]                  |
        And there are following attributes:
            | name               | presentation   | type     |
            | T-Shirt collection | Collection     | text     |
            | T-Shirt fabric     | T-Shirt fabric | text     |
            | Bag material       | Material       | text     |
            | Beverage calories  | Calories       | integer  |
            | Coffee caffeine    | Caffeine       | checkbox |
        And there is archetype "T-Shirt" with following configuration:
            | code       | Arch1                              |
            | options    | O1, O2                             |
            | attributes | T-Shirt collection, T-Shirt fabric |
        And there is archetype "Beverage" with following configuration:
            | code       | Arch2             |
            | options    | O4, O5            |
            | attributes | Beverage calories |
        And I am logged in as administrator

    Scenario: Seeing index of all archetypes
        Given I am on the dashboard page
        When I follow "Product archetypes"
        Then I should be on the product archetype index page
        And I should see 2 archetypes in the list

    Scenario: Seeing empty index of archetypes
        Given there are no product archetypes
        When I am on the product archetype index page
        Then I should see "There are no archetypes defined"

    Scenario: Accessing the archetype creation form
        Given I am on the dashboard page
        When I follow "Product archetypes"
        And I follow "Create archetype"
        Then I should be on the product archetype creation page

    Scenario: Submitting form without specifying the name
        Given I am on the product archetype creation page
        When I press "Create"
        Then I should still be on the product archetype creation page
        And I should see "Please enter archetype name"

    Scenario: Creating Bag archetype with color as option
            and material as attribute
        Given I am on the product archetype creation page
        When I fill in "Code" with "bag"
        And I fill in "Name" with "Bag"
        And I select "Bag color" from "Options"
        And I select "Bag material" from "Attributes"
        And I press "Create"
        Then I should be on the product archetype index page
        And I should see "Archetype has been successfully created"

    Scenario: Creating simple T-Shirt archetype with color and size
            as options but without attributes
        Given I am on the product archetype creation page
        When I fill in "Code" with "simple_t_shirt"
        And I fill in "Name" with "Simple T-Shirt"
        And I select "T-Shirt color" from "Options"
        And I additionally select "T-Shirt size" from "Options"
        And I press "Create"
        Then I should be on the product archetype index page
        And I should see "Archetype has been successfully created"

    @javascript
    Scenario: Creating a product by building archetype
        Given I am on the product archetype index page
        And I click "Create product" near "T-Shirt"
        When I fill in the following:
            | Name                       | Manchester United tee   |
            | sylius_product_legacy_code | BOSTON_TEE              |
            | Description                | Interesting description |
        And I go to "Attributes" tab
        And I fill in the following:
            | T-Shirt fabric     | Cotton 100%          |
            | T-Shirt collection | Champions League '11 |
        And I press "Create"
        Then I should be on the page of product "Manchester United tee"
        And I should see "Product has been successfully created"
        And "T-Shirt fabric" should appear on the page

    Scenario: Updating the archetype
        Given I am editing product archetype "T-Shirt"
        When I fill in "Name" with "Turbo T-Shirt"
        And I press "Save changes"
        Then I should be on the product archetype index page
        And I should see "Archetype has been successfully updated"

    Scenario: Parent archetype choices list
        Given I am editing product archetype "T-Shirt"
        Then I should not see Parent "T-Shirt" as available choice
        And I should see Parent "Beverage" as available choice

    Scenario: Inheriting the properties from parent archetype to a child archetype
        Given I am on the product archetype creation page
        When I fill in "Code" with "coffee"
        And I fill in "Name" with "Coffee"
        And I select "Beverage" from "Parent"
        And I select "Coffee variety" from "Options"
        And I select "Coffee caffeine" from "Attributes"
        And I press "Create"
        Then I should be on the product archetype index page
        And I should see "Archetype has been successfully created"

    @javascript
    Scenario: Deleted archetype disappears from the list
        Given I am on the product archetype index page
        When I click "Delete" near "T-Shirt"
        And I click "Delete" from the confirmation modal
        Then I should be on the product archetype index page
        And I should see "Archetype has been successfully deleted"
        And I should not see archetype with name "T-Shirt" in the list

    Scenario: Cannot update archetype code
        When I am editing product archetype "T-Shirt"
        Then the code field should be disabled

    Scenario: Try add archetype with existing code
        Given I am on the product archetype creation page
        When I fill in "Name" with "Coffee"
        And I fill in "Code" with "Arch1"
        And I press "Create"
        Then I should still be on the product archetype creation page
        And I should see "Archetype with given code already exists"

    Scenario: Try create archetype without code
        Given I am on the product archetype creation page
        When I fill in "Name" with "Coffee"
        And I press "Create"
        Then I should still be on the product archetype creation page
        And I should see "Please enter archetype code"
