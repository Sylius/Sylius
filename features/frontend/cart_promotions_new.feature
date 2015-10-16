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
            #adding promotions
          And the following promotions exist:
            | name               | description     | not-active |
            | Discount on Dress  | 50offDress      | true       |
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

Scenario Outline: Somexamples should pass
            Given I have empty order
              And I add <basketContent> to the order
              And I have <activePromotions> promotions activated
             When I apply promotions
             Then I should have <discountName> discount equal <discountValue>
              And Total price should be <totalPrice>

        Examples:
        | basketContent   | activePromotions    | discountName | discountValue | totalPrice |
        | Dr500:2,Ja200:1 | "Discount on Dress" | "50offDress" | -500.00       | 700        |
        | Dr500:1         | "Discount on Dress" | "50offDress" | -250.00       | 250        |
        | Dr125:2,Dr20:1  | "Discount on Dress" | "50offDress" | -135.00       | 135        |
        | Dr500:1,Ja200:1 | "Discount on Dress" | "50offDress" | -250.00       | 450        |

    Scenario: Discount should not be applied if promotion is not active
        Given I have empty order
          And I add "3" product of "Ja25" type
         When I apply promotions
         Then I should have no discount
          And Total price should be 75.00
