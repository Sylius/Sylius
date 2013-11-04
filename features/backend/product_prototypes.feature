@products
Feature: Product prototypes
    In order to create similar products faster
    As a store owner
    I want to be able to create prototypes

    Background:
        Given I am logged in as administrator
          And there are following options:
            | name          | presentation | values                |
            | T-Shirt color | Color        | Red, Blue, Green      |
            | T-Shirt size  | Size         | S, M, L               |
            | Bag color     | Color        | Black, Light balsamic |
          And there are following properties:
            | name               | presentation   |
            | T-Shirt collection | Collection     |
            | T-Shirt fabric     | T-Shirt fabric |
            | Bag material       | Material       |
          And there is prototype "T-Shirt" with following configuration:
            | options    | T-Shirt color, T-Shirt size        |
            | properties | T-Shirt collection, T-Shirt fabric |

    Scenario: Seeing index of all prototypes
        Given I am on the dashboard page
         When I follow "Product prototypes"
         Then I should be on the prototype index page
          And I should see 1 prototype in the list

    Scenario: Seeing empty index of prototypes
        Given there are no prototypes
         When I am on the prototype index page
         Then I should see "There are no prototypes defined"

    Scenario: Accessing the prototype creation form
        Given I am on the dashboard page
         When I follow "Product prototypes"
          And I follow "Create prototype"
         Then I should be on the prototype creation page

    Scenario: Submitting form without specifying the name
        Given I am on the prototype creation page
         When I press "Create"
         Then I should still be on the prototype creation page
          And I should see "Please enter prototype name."

    Scenario: Creating Bag prototype with color as option
              and material as property
        Given I am on the prototype creation page
         When I fill in "Name" with "Bag"
          And I select "Bag color" from "Options"
          And I select "Bag material" from "Properties"
          And I press "Create"
         Then I should be on the prototype index page
          And I should see "Prototype has been successfully created."

    Scenario: Creating simple T-Shirt prototype with color and size
              as options but without properties
        Given I am on the prototype creation page
         When I fill in "Name" with "Simple T-Shirt"
          And I select "T-Shirt color" from "Options"
          And I additionally select "T-Shirt size" from "Options"
          And I press "Create"
         Then I should be on the prototype index page
          And I should see "Prototype has been successfully created."

    Scenario: Accessing the product building form from the list
        Given I am on the prototype index page
         When I click "Build" near "T-Shirt"
         Then I should be building prototype "T-Shirt"

    Scenario: Creating a product by building prototype
        Given I am building prototype "T-Shirt"
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

    Scenario: Accessing the editing form from the list
        Given I am on the prototype index page
         When I click "edit" near "T-Shirt"
         Then I should be editing prototype "T-Shirt"

    Scenario: Updating the prototype
        Given I am editing prototype "T-Shirt"
         When I fill in "Name" with "Turbo T-Shirt"
          And I press "Save changes"
         Then I should be on the prototype index page
          And I should see "Prototype has been successfully updated."

    Scenario: Deleted prototype disappears from the list
        Given I am on the prototype index page
         When I click "delete" near "T-Shirt"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the prototype index page
          And I should see "There are no prototypes defined"

    @javascript
    Scenario: Deleted prototype disappears from the list with js modal
        Given I am on the prototype index page
         When I click "delete" near "T-Shirt"
          And I click "delete" from the confirmation modal
         Then I should be on the prototype index page
          And I should see "There are no prototypes defined"
