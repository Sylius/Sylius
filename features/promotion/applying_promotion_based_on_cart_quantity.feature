@promotion
Feature: Applying promotion based on cart quantity
  In order to pay decreased amount for my order during promotion
  As a Visitor
  I want to have promotion applied to my cart when my cart quantity qualifies

  Background:
    Given the store is operating on a single "France" channel
    And the store has a product "PHP T-Shirt" priced at "€100.00"
    And there is a promotion "Holiday promotion"

  @todo
  Scenario: Applying discount when cart quantity is above the quantity from criteria and should be above
    Given the promotion gives "€10.00" fixed discount to every cart with quantity above 2
    When I add 3 products "PHP T-Shirt" to the cart
    Then my cart total should be "€290.00"
    And my cart promotions should be "-€10.00"

  @todo
  Scenario: Not applying discount when cart quantity is equal to the amount from criteria but should be above
    Given the promotion gives "€10.00" fixed discount to every cart with quantity above 2
    When I add 2 products "PHP T-Shirt" to the cart
    Then my cart total should be "€200.00"
    And my cart promotions should be "€0.00"

  @todo
  Scenario: Not applying discount when cart quantity is lesser than the amount from criteria but should be above
    Given the promotion gives "€10.00" fixed discount to every cart with quantity above 2
    When I add product "PHP T-Shirt" to the cart
    Then my cart total should be "€100.00"
    And my cart promotions should be "€0.00"

  @todo
  Scenario: Applying discount on cart with multiple products when cart quantity is above the quantity from criteria
    Given the store has a product "Symfony T-Shirt" priced at "€50.00"
    And the promotion gives "€10.00" fixed discount to every cart with quantity above 2
    When I add 2 products "PHP T-Shirt" to the cart
    And I add product "Symfony T-Shirt" to the cart
    Then my cart total should be "€240.00"
    And my cart promotions should be "-€10.00"

  @todo
  Scenario: Applying discount when cart quantity is above the quantity from criteria and should be above
    Given the promotion gives "€10.00" fixed discount to every cart with quantity equal or above 2
    When I add 3 products "PHP T-Shirt" to the cart
    Then my cart total should be "€290.00"
    And my cart promotions should be "-€10.00"

  @todo
  Scenario: Applying discount when cart quantity is equal to the amount from criteria and should be equal or above
    Given the promotion gives "€10.00" fixed discount to every cart with quantity equal or above 2
    When I add 2 products "PHP T-Shirt" to the cart
    Then my cart total should be "€190.00"
    And my cart promotions should be "-€10.00"
