@legacy @cart
Feature: Cart
    In order to select products for purchase
    As a visitor
    I want to be able to add products to cart

    Background:
        Given store has default configuration
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
        And taxon "Category" has following children:
            | Clothing[TX1] > T-Shirts[TX2]     |
            | Clothing[TX1] > PHP T-Shirts[TX3] |
        And there are following options:
            | code | name          | values                          |
            | O1   | T-Shirt color | Red[OV1], Blue[OV2], Green[OV3] |
            | O2   | T-Shirt size  | S[OV4], M[OV5], L[OV6]          |
        And the following products exist:
            | name          | price | options | taxons       | variants selection |
            | Super T-Shirt | 20.00 | O2, O1  | T-Shirts     | match              |
            | Black T-Shirt | 19.99 | O2      | T-Shirts     |                    |
            | Git T-Shirt   | 29.99 | O2      | PHP T-Shirts | match              |
            | PHP Top       | 5.99  |         | PHP T-Shirts |                    |
            | iShirt        | 18.99 |         | T-Shirts     |                    |
        And all products are available in all variations
        And all products are assigned to the default channel
        And the default channel has following configuration:
            | taxon    |
            | Category |

    Scenario: Seeing empty cart
        Given I am on the store homepage
        When I follow "View cart"
        Then I should be on the cart summary page
        And I should see "Your cart is empty"

    Scenario: Adding simple product to cart via list
        Given I am on the store homepage
        And I follow "T-Shirts"
        When I press "Add to cart"
        Then I should be on the cart summary page
        And I should see "Item has been added to cart"

    Scenario: Adding simple product to cart via product page
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        And I click "Git T-Shirt"
        When I press "Add to cart"
        Then I should be on the cart summary page
        And I should see "Item has been added to cart"

    Scenario: Added simple products appear on the cart summary
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        When I press "Add to cart"
        Then I should be on the cart summary page
        And I should see 1 item in the list

    Scenario: Correct unit price is displayed in cart summary
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        And I click "PHP Top"
        When I press "Add to cart"
        Then I should be on the cart summary page
        And I should see item with unit price "€5.99" in the list

    Scenario: Correct cart total is displayed after adding the item
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        And I click "PHP Top"
        When I press "Add to cart"
        Then I should be on the cart summary page
        And I should see 1 item in the list
        And "Grand total: €5.99" should appear on the page

    Scenario: Adding item and specifying the quantity
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        And I click "PHP Top"
        When I fill in "Quantity" with "3"
        And I press "Add to cart"
        Then I should be on the cart summary page
        And I should see 1 item in the list
        And "Grand total: €17.97" should appear on the page

    Scenario: Correct item total is displayed for each item
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        And I click "PHP Top"
        When I fill in "Quantity" with "2"
        And I press "Add to cart"
        Then I should be on the cart summary page
        And I should see item with total "€11.98" in the list

    Scenario: Adding product to cart by selecting just one option
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        And I click "Git T-Shirt"
        When I select "M" from "T-Shirt size"
        And I press "Add to cart"
        Then I should be on the cart summary page
        And I should see "Item has been added to cart"

    Scenario: Adding product to cart by selecting multiple options
        Given I am on the store homepage
        And I follow "T-Shirts"
        And I click "Super T-Shirt"
        When I select "S" from "T-Shirt size"
        And I select "Blue" from "T-Shirt color"
        And I press "Add to cart"
        Then I should be on the cart summary page
        And I should see "Item has been added to cart"

    Scenario: Products added by selecting options appear on the cart summary
        Given I am on the store homepage
        And I follow "T-Shirts"
        And I click "Super T-Shirt"
        When I select "S" from "T-Shirt size"
        And I select "Blue" from "T-Shirt color"
        And I press "Add to cart"
        Then I should be on the cart summary page
        And I should see 1 item in the list

    Scenario: Correct cart total is displayed after adding the item
            by selecting options
        Given I am on the store homepage
        And I follow "T-Shirts"
        And I click "Super T-Shirt"
        When I select "S" from "T-Shirt size"
        And I select "Blue" from "T-Shirt color"
        And I press "Add to cart"
        Then I should be on the cart summary page
        And "Grand total: €20.00" should appear on the page

    Scenario: Adding same variant twice does not create new item
        Given I am on the product page for "Super T-Shirt"
        And I select "M" from "T-Shirt size"
        And I press "Add to cart"
        When I go to the product page for "Super T-Shirt"
        And I select "M" from "T-Shirt size"
        And I press "Add to cart"
        Then I should be on the cart summary page
        And I should see 1 cart item in the list
        And "Grand total: €40.00" should appear on the page

    Scenario: Adding same variant twice sums the quantities
        Given I am on the product page for "Super T-Shirt"
        And I select "M" from "T-Shirt size"
        And I press "Add to cart"
        When I go to the product page for "Super T-Shirt"
        And I fill in "Quantity" with "5"
        And I select "M" from "T-Shirt size"
        And I press "Add to cart"
        Then I should be on the cart summary page
        And I should see 1 cart item in the list
        And "Grand total: €120.00" should appear on the page
