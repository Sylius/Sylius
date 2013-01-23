Feature: Shipping methods
    As a store owner
    I want to be able to configure shipping methods
    In order to apply proper shipping to my merchandise

    Background:
        Given I am logged in as administrator
          And there are following shipping categories:
            | name    |
            | Regular |
            | Heavy   |
          And the following shipping methods exist:
            | category | name  |
            | Regular  | FedEx |
            | Heavy    | DHL   |

    Scenario: Seeing index of all shipping methods
        Given I am on the dashboard page
         When I follow "Shipping methods"
         Then I should be on the shipping method index page
          And I should see 2 shipping methods in the list

    Scenario: Seeing empty index of shipping methods
        Given there are no shipping methods
          And I am on the shipping method index page
         Then I should see "There are no shipping methods configured"

    Scenario: Accessing the shipping method creation form
        Given I am on the dashboard page
         When I follow "Shipping methods"
          And I follow "Create shipping method"
         Then I should be on the shipping method creation page

    Scenario: Submitting invalid form
        Given I am on the shipping method creation page
         When I press "Create"
         Then I should still be on the shipping method creation page
          And I should see "Please enter shipping method name"

    @javascript
    Scenario: Creating new shipping method with flat rate per item
        Given I am on the shipping method creation page
          And I fill in "Name" with "FedEx World Shipping"
          And I select "Flat rate per item" from "Calculator"
          And I fill in "Amount" with "10"
         When I press "Create"
         Then I should be on the page of shipping method "FedEx World Shipping"
          And I should see "Method has been successfully created."

    @javascript
    Scenario: Creating new shipping method with flat rate per shipment
        Given I am on the shipping method creation page
          And I fill in "Name" with "FedEx World Shipping"
          And I select "Flat rate per shipment" from "Calculator"
          And I fill in "Amount" with "10"
         When I press "Create"
         Then I should be on the page of shipping method "FedEx World Shipping"
          And I should see "Method has been successfully created."

    @javascript
    Scenario: Creating new shipping method with flexible rate
        Given I am on the shipping method creation page
          And I fill in "Name" with "FedEx World Shipping"
          And I select "Flexible rate" from "Calculator"
          And I fill in "First item cost" with "100"
          And I fill in "Additional item cost" with "10"
          And I fill in "Limit additional items" with "5"
         When I press "Create"
         Then I should be on the page of shipping method "FedEx World Shipping"
          And I should see "Method has been successfully created."

    Scenario: Created shipping methods appear in the list
        Given I created shipping method "FedEx World Shipping" for category "Regular"
          And I go to the shipping method index page
         Then I should see 3 shipping methods in the list
          And I should see shipping method with name "FedEx World Shipping" in that list

    Scenario: Updating the shipping method
        Given I am on the page of shipping method "FedEx"
          And I follow "Edit"
         When I fill in "Name" with "General Shipping"
          And I press "Save changes"
         Then I should be on the page of shipping method "General Shipping"

    Scenario: Deleting shipping method
        Given I am on the page of shipping method "FedEx"
         When I follow "Delete"
         Then I should be on the shipping method index page
          And I should see "Method has been successfully deleted."

    Scenario: Deleted shipping method disappears from the list
        Given I am on the page of shipping method "FedEx"
         When I follow "Delete"
         Then I should be on the shipping method index page
          And I should not see shipping method with name "FedEx" in that list

    Scenario: Displaying the shipping method details
        Given I am on the page of shipping method "FedEx"
         Then I should see "Regular"
          And I should see "per_item_rate"
