@legacy @promotion
Feature: Checkout coupon promotions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given store has default configuration
        And the following promotions exist:
            | code | name              | description            |
            | P1   | Press campaign    | Coupon based promotion |
            | P2   | New Year campaign | Coupon based promotion |
        And promotion "Press campaign" has following rules defined:
            | type       | configuration |
            | Item total | Amount: 100   |
        And promotion "Press campaign" has following coupons:
            | code   | usage limit | used |
            | XD0001 | 1           | 0    |
        And promotion "Press campaign" has following actions defined:
            | type                 | configuration |
            | Order fixed discount | Amount: 5     |
        And promotion "New Year campaign" has following rules defined:
            | type          | configuration |
            | Cart quantity | Count: 2      |
        And promotion "New Year campaign" has following actions defined:
            | type                 | configuration |
            | Order fixed discount | Amount: 10    |
        And promotion "New Year campaign" has following coupons:
            | code   | usage limit | used |
            | XD0002 | 1           | 1    |
        And there are following taxons defined:
            | code | name     |
            | RTX2 | Category |
        And taxon "Category" has following children:
            | Clothing[TX1] > Debian T-Shirts[TX2] |
        And the following products exist:
            | name   | price | taxons          |
            | Buzz   | 500   | Debian T-Shirts |
            | Potato | 200   | Debian T-Shirts |
            | Woody  | 125   | Debian T-Shirts |
            | Sarge  | 25    | Debian T-Shirts |
            | Etch   | 20    | Debian T-Shirts |
            | Lenny  | 1     | Debian T-Shirts |
        And all products are assigned to the default channel
        And all promotions are assigned to the default channel

    Scenario: Promotion with coupons is applied when the customer
            has added a valid coupon
        Given I am on the store homepage
        And I added product "Etch" to cart, with quantity "6"
        When I fill in "Coupon" with "XD0001"
        And I press "Save"
        Then I should be on the cart summary page
        And I should see "Your promotion coupon has been added to the cart"
        And "Promotion total: -€5.00" should appear on the page
        And "Grand total: €115.00" should appear on the page

    Scenario: An invalid coupon can not be added to the cart
        Given I am on the store homepage
        And I added product "Etch" to cart, with quantity "5"
        When I fill in "Coupon" with "an invalid coupon"
        And I press "Save"
        Then I should be on the cart summary page
        And I should see "Your promotion coupon is not valid"
        And "Promotion total" should not appear on the page
        And "Grand total: €100.00" should appear on the page

    Scenario: A valid coupon can not be added to the cart if the cart does
            not fulfill the rules required by the promotion
        Given I am on the store homepage
        And I added product "Etch" to cart, with quantity "4"
        When I fill in "Coupon" with "XD0001"
        And I press "Save"
        Then I should be on the cart summary page
        And I should see "Your cart is not eligible to this promotion coupon"
        And "Promotion total" should not appear on the page
        And "Grand total: €80.00" should appear on the page

    Scenario: A valid coupon can not be added to the cart if its usage
            limit has been reached
        Given I am on the store homepage
        And I added product "Lenny" to cart
        And I added product "Etch" to cart
        And I added product "Sarge" to cart
        When I fill in "Coupon" with "XD0002"
        And I press "Save"
        Then I should be on the cart summary page
        And I should see "Your cart is not eligible to this promotion coupon"
        And "Promotion total" should not appear on the page
        And "Grand total: €46.00" should appear on the page
