@legacy @promotion
Feature: Checkout product promotion
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given store has default configuration
        And the following products exist:
            | name   | price |
            | Lenny  | 15    |
            | Buzz   | 500   |
            | Potato | 200   |
            | Etch   | 20    |
            | Woody  | 125   |
            | Sarge  | 25    |
            | Ubu    | 200   |
        And the following promotions exist:
            | code | name         | description                      |
            | P1   | Free product | Almost free product over 100 eur |
        And promotion "Free product" has following rules defined:
            | type       | configuration |
            | Item total | Amount: 100   |
        And promotion "Free product" has following actions defined:
            | type        | configuration                   |
            | Add product | variant:Ubu,quantity:1,price:10 |
        And all products are assigned to the default channel
        And all promotions are assigned to the default channel

    Scenario: Free product is not applied when the cart
            has not the required amount
        Given I am on the store homepage
        When I add product "Etch" to cart, with quantity "1"
        Then I should be on the cart summary page
        And I should not see product "Ubu" in the cart summary
        And "Grand total: €20.00" should appear on the page

    Scenario: Free product is applied when the cart has the
            required amount
        Given I am on the store homepage
        When I add product "Potato" to cart, with quantity "3"
        Then I should be on the cart summary page
        And I should see product "Ubu" in the cart summary
        And "Grand total: €610.00" should appear on the page
