@promotion
Feature: Receiving discount based on cart quantity
  In order to pay decreased amount for my order during promotion
  As a Visitor
  I want to have promotion applied to my cart when my cart quantity qualifies

  Background:
    Given the store is operating on a single "France" channel
    And the store has a product "PHP T-Shirt" priced at "€100.00"
    And there is a promotion "Holiday promotion"

  @todo
  Scenario: Receiving discount when buying more than required quantity
    Given the promotion gives "€10.00" fixed discount to every cart with quantity at least 3
    When I add 4 products "PHP T-Shirt" to the cart
    Then my cart total should be "€390.00"
    And my discount should be "-€10.00"

  @todo
  Scenario: Receiving discount when buying the required quantity
    Given the promotion gives "€10.00" fixed discount to every cart with quantity at least 3
    When I add 3 products "PHP T-Shirt" to the cart
    Then my cart total should be "€290.00"
    And my discount should be "-€10.00"

  @todo
  Scenario: Not receiving discount when buying less than required quantity
    Given the promotion gives "€10.00" fixed discount to every cart with quantity at least 3
    When I add 2 products "PHP T-Shirt" to the cart
    Then my cart total should be "€200.00"
    And my discount should be "€0.00"

  @todo
  Scenario: Receiving discount when buying different products with the required quantity
    Given the store has a product "Symfony T-Shirt" priced at "€50.00"
    And the promotion gives "€10.00" fixed discount to every cart with quantity at least 3
    When I add 2 products "PHP T-Shirt" to the cart
    And I add product "Symfony T-Shirt" to the cart
    Then my cart total should be "€240.00"
    And my discount should be "-€10.00"
