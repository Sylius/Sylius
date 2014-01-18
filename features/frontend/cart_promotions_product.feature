@promotions
Feature: Checkout product promotion
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given the following products exist:
          | name    | price |
          | Buzz    | 500   |
          | Potato  | 200   |
          | Etch    | 20    |
		  | Woody   | 125   |
          | Sarge   | 25    |
          | Lenny   | 15    |
          | Ubu     | 200   |
        And the following promotions exist:
          | name                | description                      |
          | Free product        | Almost free product over 100 eur |
        And promotion "Free product" has following rules defined:
          | type       | configuration |
          | Item total | Amount: 100   |
        And promotion "Free product" has following actions defined:
          | type        | configuration                    |
          | Add product | variant:Lenny,quantity:1,price:10 |

    Scenario: Free product is applied when the cart has the
              required amount
        Given I am on the store homepage
         When I add product "Potato" to cart, with quantity "3"
         Then I should be on the cart summary page
          And "Lenny" should appear on the page
          And "Grand total: €610.00" should appear on the page

    Scenario: Free product is not applied when the cart
              has not the required amount
        Given I am on the store homepage
         When I add product "Etch" to cart, with quantity "1"
         Then I should be on the cart summary page
          And "Lenny" should not appear on the page
          And "Grand total: €20.00" should appear on the page
