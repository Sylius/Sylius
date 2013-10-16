@promotions
Feature: Promotions
    In order to apply discounts to my products
    As a store owner
    I want to be able to manage promotions

    Background:
        Given I am logged in as administrator
          And the following promotions exist:
            | name           | description                            | usage limit | used | starts     | ends       |
            | New Year       | New Year Sale for more than 3 items    |             |      | 2013-12-31 | 2014-01-03 |
            | Christmas      | Christmas Sale for orders over 100 EUR |             |      | 2013-12-10 | 2013-12-25 |
            | Press Campaign | Coupon based promotion                 |             |      |            |            |
            | Free orders    | First 3 orders have 100% discount!     | 3           | 0    |            |            |
          And promotion "New Year" has following rules defined:
            | type       | configuration |
            | Item count | Count: 3      |
          And promotion "New year" has following actions defined:
            | type           | configuration |
            | Fixed discount | Amount: 10    |
          And promotion "Christmas" has following rules defined:
            | type       | configuration |
            | Item total | Amount: 100   |
          And promotion "Christmas" has following actions defined:
            | type           | configuration |
            | Fixed discount | Amount: 15    |
          And promotion "Press Campaign" has following actions defined:
            | type           | configuration |
            | Fixed discount | Amount: 5     |
          And promotion "Press Campaign" has following coupons:
            | code   | usage limit | used |
            | XD0001 | 1           | 0    |
            | XD0002 | 1           | 1    |
            | XD0003 | 1           | 0    |
            | AD0001 | 3           | 2    |
            | AD0002 | 3           | 0    |
          And promotion "Free orders" has following actions defined:
            | type                | configuration   |
            | Percentage discount | Percentage: 100 |

    Scenario: Seeing index of all promotions
        Given I am on the dashboard page
         When I follow "Promotions"
         Then I should be on the promotion index page
          And I should see 4 promotions in the list

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
    Scenario: Creating new promotion with item total rule
        Given I am on the promotion creation page
         When I fill in "Name" with "Behat Training"
          And I fill in "Description" with "Behat Training discount for orders over 5000 EUR"
          And I click "Add rule"
          And I select "Item total" from "Type"
          And I fill in "Amount" with "5000"
          And I press "Create"
         Then I should be on the page of promotion "Behat Training"
          And I should see "Promotion has been successfully created."

    @javascript
    Scenario: Creating new promotion with item count rule
        Given I am on the promotion creation page
         When I fill in "Name" with "Behat Training"
          And I fill in "Description" with "Behat Training Sale for 10 and more people"
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

    @javascript
    Scenario: Creating new promotion with percentage discount action
        Given I am on the promotion creation page
         When I fill in "Name" with "Sylius Training"
          And I fill in "Description" with "Sylius Training 10% discount on all orders"
          And I click "Add action"
          And I select "Percentage discount" from "Type"
          And I fill in "Percentage" with "10"
          And I press "Create"
         Then I should be on the page of promotion "Sylius Training"
          And I should see "Promotion has been successfully created."

    Scenario: Adding coupon manually
        Given I am on the page of promotion "Press Campaign"
          And I follow "Add coupon"
         When I fill in "Code" with "SPECIAL"
          And I press "Create"
         Then I should be on the page of promotion "Press Campaign"
          And I should see "Promotion coupon has been successfully created."

    Scenario: Added coupon appears on the list of coupons
        Given I am on the page of promotion "Press Campaign"
          And I follow "Add coupon"
          And I fill in "Code" with "SPECIAL"
          And I press "Create"
         When I follow "List coupons"
         Then I should see 6 coupons in the list

    Scenario: Coupon code is required
        Given I am on the page of promotion "Press Campaign"
          And I follow "Add coupon"
         When I press "Create"
         Then I should see "Please enter coupon code."

    Scenario: Coupon usage limit must be at least 1
        Given I am on the page of promotion "Press Campaign"
          And I follow "Add coupon"
          And I fill in "Usage limit" with "-2"
         When I press "Create"
         Then I should see "Coupon usage limit must be at least 1."

    Scenario: Adding coupon with usage limit
        Given I am on the page of promotion "Press Campaign"
          And I follow "Add coupon"
         When I fill in "Code" with "SPECIAL"
          And I fill in "Usage limit" with "5"
          And I press "Create"
         Then I should be on the page of promotion "Press Campaign"
          And I should see "Promotion coupon has been successfully created."

    Scenario: Generating coupons
        Given I am on the page of promotion "Press Campaign"
          And I follow "Generate coupons"
         When I fill in "Amount" with "50"
          And I press "Generate"
         Then I should see "Promotion coupons have been successfully generated."

    Scenario: Generating coupons with usage limit
        Given I am on the page of promotion "Press Campaign"
          And I follow "Generate coupons"
         When I fill in "Amount" with "5"
          And I fill in "Usage limit" with "5"
          And I press "Generate"
         Then I should see "Promotion coupons have been successfully generated."

    Scenario: Generated coupon appears on the list of coupons
        Given I am on the page of promotion "Press Campaign"
          And I follow "Generate coupons"
          And I fill in "Amount" with "50"
          And I press "Generate"
         Then I should see "Promotion coupons have been successfully generated."
          And I should see "Total: 55"

    Scenario: Amount of coupons to generate is required
        Given I am on the page of promotion "Press Campaign"
          And I follow "Generate coupons"
          And I leave "Amount" field blank
         When I press "Generate"
         Then I should see "Please enter amount of coupons to generate."

    Scenario: Amount of coupons to generate must be at least 1
        Given I am on the page of promotion "Press Campaign"
          And I follow "Generate coupons"
          And I fill in "Amount" with "-4"
         When I press "Generate"
         Then I should see "Amount of coupons to generate must be at least 1."

    Scenario: Usage limit of coupons generated must be at least 1
        Given I am on the page of promotion "Press Campaign"
          And I follow "Generate coupons"
          And I fill in "Usage limit" with "-4"
         When I press "Generate"
         Then I should see "Usage limit of generated coupons must be at least 1."

    @javascript
    Scenario: Creating promotion with usage limit
        Given I am on the promotion creation page
         When I fill in "Name" with "First 5 pay half!"
          And I fill in "Description" with "First 5 orders get 50% discount!"
          And I click "Add action"
          And I select "Percentage discount" from "Type"
          And I fill in "Percentage" with "50"
          And I fill in "Usage limit" with "5"
          And I press "Create"
         Then I should be on the page of promotion "First 5 pay half!"
          And I should see "Promotion has been successfully created."

    Scenario: Created promotions appear in the list
        Given I am on the promotion creation page
          And I fill in "Name" with "First 5 pay half!"
          And I fill in "Description" with "First 5 orders get 50% discount!"
          And I press "Create"
         When I go to the promotion index page
         Then I should see 5 promotions in the list
          And I should see promotion with name "First 5 pay half!" in that list

    Scenario: Accessing the promotion editing form
        Given I am on the page of promotion "New Year"
         When I follow "edit"
         Then I should be editing promotion "New Year"

    Scenario: Accessing the editing form from the list
        Given I am on the promotion index page
         When I click "edit" near "New Year"
         Then I should be editing promotion "New Year"

    Scenario: Updating the promotion
        Given I am editing promotion "New Year"
         When I fill in "Name" with "New Year Sale"
          And I press "Save changes"
         Then I should be on the page of promotion "New Year Sale"

    Scenario: Deleting promotion
        Given I am on the page of promotion "New Year"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the promotion index page
          And I should see "Promotion has been successfully deleted."

    @javascript
    Scenario: Deleting promotion with js modal
        Given I am on the page of promotion "New Year"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the promotion index page
          And I should see "Promotion has been successfully deleted."

    Scenario: Deleted promotion disappears from the list
        Given I am on the page of promotion "New Year"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the promotion index page
          And I should not see promotion with name "New Year" in that list

    @javascript
    Scenario: Deleted promotion disappears from the list with js modal
        Given I am on the page of promotion "New Year"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the promotion index page
          And I should not see promotion with name "New Year" in that list

    Scenario: Deleting promotion via list
        Given I am on the promotion index page
         When I click "delete" near "Press Campaign"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the promotion index page
          And I should see "Promotion has been successfully deleted."

    @javascript
    Scenario: Deleting promotion via list with js modal
        Given I am on the promotion index page
         When I click "delete" near "Press Campaign"
          And I click "delete" from the confirmation modal
         Then I should be on the promotion index page
          And I should see "Promotion has been successfully deleted."

    Scenario: Deleting promotion rule
        Given I am on the page of promotion "Christmas"
         When I press "delete" near "Item total"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the page of promotion "Christmas"
          And I should see "Promotion rule has been successfully deleted."
          And I should not see "Order total"

    @javascript
    Scenario: Deleting promotion rule with js modal
        Given I am on the page of promotion "Christmas"
         When I press "delete" near "Item total"
          And I click "delete" from the confirmation modal
         Then I should be on the page of promotion "Christmas"
          And I should see "Promotion rule has been successfully deleted."
          And I should not see "Order total"

    Scenario: Deleting promotion action
        Given I am on the page of promotion "Christmas"
         When I press "delete" near "Fixed discount"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the page of promotion "Christmas"
          And I should see "Promotion action has been successfully deleted."
          And I should not see "Fixed discount"

    @javascript
    Scenario: Deleting promotion action with js modal
        Given I am on the page of promotion "Christmas"
         When I press "delete" near "Fixed discount"
          And I click "delete" from the confirmation modal
         Then I should be on the page of promotion "Christmas"
          And I should see "Promotion action has been successfully deleted."
          And I should not see "Fixed discount"
