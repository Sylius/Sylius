@promotion
Feature: Applying promotion based on items total
  In order to pay decreased amount for my order during promotion
  As a Visitor
  I want to have promotion applied when my items total is qualified

  Background:
    Given the store is operating on a single "France" channel
    And the store has a product "PHP T-Shirt" priced at "€100.00"
    And there is a promotion "Holiday promotion"

  @todo
  Scenario: Applying discount when items total is above the amount from criteria and should be above
    Given the promotion gives "€10.00" fixed discount to every cart with items total above "€80.00"
    When I add product "PHP T-Shirt" to the cart
    Then my cart total should be "€90.00"
    And my cart promotions should be "-€10.00"

  @todo
  Scenario: Not applying discount when items total is equal to the amount from criteria but should be above
    Given the promotion gives "€10.00" fixed discount to every cart with items total above "€100.00"
    When I add product "PHP T-Shirt" to the cart
    Then my cart total should be "€100.00"
    And my cart promotions should be "€0.00"

  @todo
  Scenario: Not applying discount when items total is lesser then the amount from criteria but should be above
    Given the promotion gives "€10.00" fixed discount to every cart with items total above "€120.00"
    When I add product "PHP T-Shirt" to the cart
    Then my cart total should be "€120.00"
    And my cart promotions should be "€0.00"

  @todo
  Scenario: Applying discount on cart with multiple quantity when items total is above the amount from criteria
    Given the promotion gives "€10.00" fixed discount to every cart with items total above "€120.00"
    When I add 2 products "PHP T-Shirt" to the cart
    Then my cart total should be "€190.00"
    And my cart promotions should be "-€10.00"

  @todo
  Scenario: Applying discount when items total is above the amount from criteria
    Given the promotion gives "€10.00" fixed discount to every cart with items total equal or above "€80.00"
    When I add product "PHP T-Shirt" to the cart
    Then my cart total should be "€90.00"
    And my cart promotions should be "-€10.00"

  @todo
  Scenario: Applying discount when items total is equal to the amount from criteria
    Given the promotion gives "€10.00" fixed discount to every cart with items total equal or above "€100.00"
    When I add product "PHP T-Shirt" to the cart
    Then my cart total should be "€90.00"
    And my cart promotions should be "-€10.00"
