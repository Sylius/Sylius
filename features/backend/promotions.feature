Feature: Promotions
    As a store owner
    I want to be able to manage promotions
    In order to apply discounts to my products

    Background:
        Given I am logged in as administrator
          And the following promotion coupons are defined:
            | code | usage limit |
            | xxx  | 5000        |
          And the following promotion rules are defined:
            | type        | amount | equal |
            | order_total | 5000   | yes   |
          And the following promotion rules are defined:
            | type        | count | equal |
            | item_count  | 10    | yes   |
          And the following promotion actions are defined:
            | type           | amount |
            | fixed_discount | 20     |
          And the following promotions exist:
            | name      | description                             | coupons | rules       | actions        |
            | New Year  | New Year Sale for 10 and more items     | xxx     | item_count  | fixed_discount |
            | Christmas | Christmas Sale for orders over 5000 EUR |         | order_total | fixed_discount |

    Scenario: Seeing index of all promotions
        Given I am on the dashboard page
         When I follow "Promotions"
         Then I should be on the promotion index page
          And I should see 2 promotions in the list

    Scenario: Seeing empty index of promotions
        Given there are no promotions
         When I am on the promotion index page
         Then I should see "There are no promotions configured"

    Scenario: Accessing the promotion creation form
        Given I am on the dashboard page
         When I follow "Promotions"
          And I follow "Create promotion"
         Then I should be on the promotion creation page

    Scenario: Submitting invalid form without name
        Given I am on the promotion creation page
         When I press "Create"
         Then I should still be on the promotion creation page
          And I should see "Please enter promotion name."

    @javascript
    Scenario: Submitting invalid coupon code
        Given I am on the promotion creation page
         When I fill in "Name" with "Behat Training"
          And I fill in "Description" with "Behat Training Coupons"
          And I click "Add coupon"
          And I press "Create"
         Then I should still be on the promotion creation page
          And I should see "Please enter coupon code."

    @javascript
    Scenario: Creating new promotion with coupon
        Given I am on the promotion creation page
         When I fill in "Name" with "Behat Training"
          And I fill in "Description" with "Behat Training Coupons"
          And I click "Add coupon"
          And I fill in "Code" with "xxx"
          And I press "Create"
         Then I should be on the page of promotion "Behat Training"
          And I should see "Promotion has been successfully created."

    @javascript
    Scenario: Creating new promotion with order total rule
        Given I am on the promotion creation page
         When I fill in "Name" with "Behat Training"
          And I fill in "Description" with "Behat Training Sale for orders over 5000 EUR"
          And I click "Add rule"
          And I select "Order total" from "Type"
          And I fill in "Amount" with "5000"
          And I press "Create"
         Then I should be on the page of promotion "Behat Training"
          And I should see "Promotion has been successfully created."

    @javascript
    Scenario: Creating new promotion with item count rule
        Given I am on the promotion creation page
         When I fill in "Name" with "Behat Training"
          And I fill in "Description" with "Behat Training Sale for 10 and more items"
          And I click "Add rule"
          And I select "Item count" from "Type"
          And I fill in "Count" with "10"
          And I press "Create"
         Then I should be on the page of promotion "Behat Training"
          And I should see "Promotion has been successfully created."

    @javascript
    Scenario: Creating new promotion with fixed discount action
        Given I am on the promotion creation page
         When I fill in "Name" with "Behat Training"
          And I fill in "Description" with "Behat Training 100 EUR discount on all orders"
          And I click "Add action"
          And I select "Fixed discount" from "Type"
          And I fill in "Amount" with "100"
          And I press "Create"
         Then I should be on the page of promotion "Behat Training"
          And I should see "Promotion has been successfully created."

    Scenario: Created promotions appear in the list
        Given I created promotion "Behat Training"
          And I go to the promotion index page
         Then I should see 3 promotions in the list
          And I should see promotion with name "Behat Training" in that list

    Scenario: Accessing the promotion editing form
        Given I am on the page of promotion "New Year"
         When I follow "edit"
         Then I should be editing promotion "New Year"

    Scenario: Accessing the editing form from the list
        Given I am on the promotion index page
         When I click "edit" near "New Year"
         Then I should be editing promotion "New Year"

    @javascript
    Scenario: Updating the promotion
        Given I am editing promotion "New Year"
         When I fill in "Name" with "Behat Training"
          And I press "Save changes"
         Then I should be on the page of promotion "Behat Training"

    Scenario: Deleting promotion
        Given I am on the page of promotion "New Year"
         When I press "delete"
         Then I should be on the promotion index page
          And I should see "Promotion has been successfully deleted."

    Scenario: Deleted promotion disappears from the list
        Given I am on the page of promotion "New Year"
         When I press "delete"
         Then I should be on the promotion index page
          And I should not see promotion with name "New Year" in that list

    Scenario: Deleting promotion coupon
        Given I am on the page of promotion "New Year"
         When I press "delete" near "xxx"
         Then I should be on the page of promotion "New Year"
          And I should see "Promotion coupon has been successfully deleted."
          And I should not see "xxx"

    Scenario: Deleting promotion rule
        Given I am on the page of promotion "Christmas"
         When I press "delete" near "order_total"
         Then I should be on the page of promotion "Christmas"
          And I should see "Promotion rule has been successfully deleted."
          And I should not see "order_total"

    Scenario: Deleting promotion action
        Given I am on the page of promotion "Christmas"
         When I press "delete" near "fixed_discount"
         Then I should be on the page of promotion "Christmas"
          And I should see "Promotion action has been successfully deleted."
          And I should not see "fixed_discount"
