@promotions
Feature: Checkout product promotion
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given store has default configuration
        And there are following taxonomies defined:
            | name     |
            | Category |
        And taxonomy "Category" has following taxons:
            | Clothing > Dress  |
            | Clothing > Jacket |
        And the following products exist:
            | name    | price | taxons |
            | Dr500   | 500   | Dress  |
            | Ja200   | 200   | Jacket |
            | Dr125   | 125   | Dress  |
            | Ja25    | 25    | Jacket |
            | Dr20    | 20    | Dress  |
            | Ja15    | 15    | Jacket |
          And the following promotions exist:
            | name               | description     |
            | Discount on Dress  | 50% Only Dress  |
          And promotion "Discount on Dress" has following rules defined:
            | type             | configuration            |
            | Taxonomy         | Taxons: Dress,Exclude: 0 |
          And promotion "Discount on Dress" has following benefits defined:
            | type                  | configuration  |
            | Percentage Discount   | Percentage: 50 |
          And promotion "Discount on Dress" has following filters defined:
            | type                  | configuration          |
            | taxon_filter          | Filtered_taxon: Dress  |
          And all products are assigned to the default channel
          And all promotions are assigned to the default channel

        Scenario Outline: Somexamples
            Given I have empty order
              And I add

            Examples:
            | backetContent| activePromotions | discountName | discountValue | totalPrice |
            | Dr500:2      | activePromotions | discountName | discountValue | totalPrice |


    Scenario: Discount should not be applied if order contains no Dress products
        Given I have empty order
          And I add "3" product of "Ja25" type
         When I apply promotions
         Then I should have no discount
          And Total price should be 75.00

    Scenario: Discount should not be applied if order contains one Dress product
        Given I have empty order
          And I add "1" product of "Dr500" type
         When I apply promotions
         Then I should have "50% Only Dress" discount equal -250
          And Total price should be 250.00

    Scenario: Discount should be applied to all Dress
              products if order contains more than one Dress product
        Given I have empty order
          And I add "2" product of "Dr125" type
          And I add "1" product of "Dr20" type
         When I apply promotions
         Then I should have "50% Only Dress" discount equal -135.00
          And Total price should be 135.00

    Scenario: Discount should be applied only to Dress
              products if the order contains not only Dress products
        Given I have empty order
          And Promotion "50% Only Dress" is active
          And I add "1" product of "Dr500" type
          And I add "1" product of "Ja200" type
         When I apply promotions
         Then I should have "50% Only Dress" discount equal -250
          And Total price should be 450.00
