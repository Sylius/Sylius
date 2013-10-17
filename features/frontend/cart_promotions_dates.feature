@checkout
Feature: Checkout promotions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given the following promotions exist:
          | name      | description           | starts     | ends       |
          | Christmas | 25% off for Christmas | 2013-12-10 | 2013-12-25 |
        And promotion "Christmas" has following actions defined:
          | type                | configuration |
          | Percentage discount | Percentage: 25  |
        And there are following taxonomies defined:
          | name     |
          | Category |
        And taxonomy "Category" has following taxons:
          | Clothing > Debian T-Shirts |
        And the following products exist:
          | name    | price | taxons          |
          | Buzz    | 500   | Debian T-Shirts |
          | Potato  | 200   | Debian T-Shirts |
          | Woody   | 125   | Debian T-Shirts |
          | Sarge   | 25    | Debian T-Shirts |
          | Etch    | 20    | Debian T-Shirts |
          | Lenny   | 15    | Debian T-Shirts |

    # promotion limited in the time (Christmas)
    Scenario: Promotion is applied when the order date corresponds
              with promotion dates
    # promotion limited in the time (Christmas)
    Scenario: Promotion is not applied when the order date is outside
              promotion dates

