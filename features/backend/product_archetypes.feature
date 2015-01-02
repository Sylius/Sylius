@products
Feature: Product archetypes
    In order to create similar products faster
    As a store owner
    I want to be able to create archetypes

    Background:
        Given there is default currency configured
        And there are following locales configured:
            | code | enabled |
            | en   | yes     |
        And I am logged in as administrator
        And there are following options:
            | name             | presentation | values                     |
            | T-Shirt color    | Color        | Red, Blue, Green           |
            | T-Shirt size     | Size         | S, M, L                    |
            | Bag color        | Color        | Black, Light balsamic      |
            | Beverage size    | Size         | Tall, Grande, Venti        |
            | Beverage milk    | Milk         | None, Whole, Skinny, Soya  |
            | Coffee variety   | Variety      | Colombian, Ethiopian       |
        And there are following attributes:
            | name                  | presentation   |
            | T-Shirt collection    | Collection     |
            | T-Shirt fabric        | T-Shirt fabric |
            | Bag material          | Material       |
            | Beverage calories     | Calories       |
            | Coffee caffeine       | Caffeine       |
        And there is archetype "T-Shirt" with following configuration:
            | options    | T-Shirt color, T-Shirt size        |
            | attributes | T-Shirt collection, T-Shirt fabric |
        And there is archetype "Beverage" with following configuration:
            | options    | Beverage size, Beverage milk       |
            | attributes | Beverage calories                  |

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
        And I should see "Please enter archetype name."

    Scenario: Creating Bag archetype with color as option
              and material as attribute
        Given I am on the product archetype creation page
        When I fill in "Code" with "bag"
        And I fill in "Name" with "Bag"
        And I select "Bag color" from "Options"
        And I select "Bag material" from "Attributes"
        And I press "Create"
        Then I should be on the product archetype index page
        And I should see "Archetype has been successfully created."

    Scenario: Creating simple T-Shirt archetype with color and size
              as options but without attributes
        Given I am on the product archetype creation page
        When I fill in "Code" with "simple_t_shirt"
        And I fill in "Name" with "Simple T-Shirt"
        And I select "T-Shirt color" from "Options"
        And I additionally select "T-Shirt size" from "Options"
        And I press "Create"
        Then I should be on the product archetype index page
        And I should see "Archetype has been successfully created."

    Scenario: Creating a product by building archetype
        Given I am on the product archetype index page
        And I click "Create product" near "T-Shirt"
        When I fill in the following:
            | Name               | Manchester United tee   |
            | Description        | Interesting description |
            | Price              | 59.99                   |
            | T-Shirt fabric     | Cotton 100%             |
            | T-Shirt collection | Champions League '11    |
        And I press "Create"
        Then I should be on the page of product "Manchester United tee"
        And I should see "Product has been successfully created."
        And "T-Shirt size" should appear on the page

    Scenario: Updating the archetype
        Given I am editing product archetype "T-Shirt"
        When I fill in "Name" with "Turbo T-Shirt"
        And I press "Save changes"
        Then I should be on the product archetype index page
        And I should see "Archetype has been successfully updated."

    Scenario: Inheriting the properties from parent archetype to a child archetype
        Given I am on the product archetype creation page
        When I fill in "Code" with "coffee"
        And I fill in "Name" with "Coffee"
        And I select "Beverage" from "Parent"
        And I select "Coffee variety" from "Options"
        And I select "Coffee caffeine" from "Attributes"
        And I press "Create"
        Then I should be on the product archetype index page
        And I should see "Archetype has been successfully created."

    @javascript
    Scenario: Deleted archetype disappears from the list
        Given I am on the product archetype index page
        When I click "delete" near "T-Shirt"
        And I click "delete" from the confirmation modal
        Then I should be on the product archetype index page
        And I should see "There are no archetypes defined"
