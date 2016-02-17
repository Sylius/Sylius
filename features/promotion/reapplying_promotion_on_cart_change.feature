@promotion
Feature: Reapplying promotion on cart change
  In order to receive proper discount for my order
  As a Customer
  I want to have proper discount applied after every operation on my cart

  Background:
    Given the store is operating on a single "France" channel
    And the store has a product "PHP T-Shirt" priced at "€100.00"
    And there is a promotion "Holiday promotion"
    And I am logged in customer

  @todo
  Scenario: Not receiving discount on shipping after removing last item from cart
    Given the store has "DHL" shipping method with "€10.00" fee
    And the promotion gives "100%" percentage discount on shipping to every order
    And I have product "PHP T-Shirt" in the cart
    When I proceed selecting "DHL" shipping method
    And I remove product "PHP T-Shirt" from the cart
    Then my cart total should be "€0.00"
    And my cart shipping fee should be "€0.00"
    And my discount should be "€0.00"

  @todo
  Scenario: Receiving discount on shipping after shipping method change
    Given the store has "DHL" shipping method with "€10.00" fee
    And the store has "FedEx" shipping method with "€30.00" fee
    And the promotion gives "100%" percentage discount on shipping to every order
    And I have product "PHP T-Shirt" in the cart
    And I chose "DHL" shipping method
    When I change shipping method to "FedEx"
    Then my cart total should be "€100.00"
    And my cart shipping fee should be "€30.00"
    And my discount should be "-€30.00"

  @todo
  Scenario: Receiving discount after removing an item from the cart and then adding another one
    Given the store has a product "Symfony T-Shirt" priced at "€150.00"
    And the promotion gives "€10.00" fixed discount to every order
    And I had product "PHP T-Shirt" in the cart
    But I removed this product from the cart
    When I add product "Symfony T-Shirt" to the cart
    Then my cart total should be "€140.00"
    And my discount should be "-€10.00"

  @todo
  Scenario: Not receiving discount when cart does not meet the required total value after removing an item
    Given the promotion gives "€10.00" fixed discount to every cart with items total at least "€120.00"
    And I have 2 products "PHP T-Shirt" in the cart
    When I change "PHP T-Shirt" quantity to 1
    Then my cart total should be "€100.00"
    And my discount should be "€0.00"

  @todo
  Scenario: Not receiving discount when cart does not meet the required quantity after removing an item
    Given the promotion gives "€10.00" fixed discount to every cart with quantity at least 3
    And I have 3 products "PHP T-Shirt" in the cart
    When I change "PHP T-Shirt" quantity to 1
    Then my cart total should be "€100.00"
    And my discount should be "€0.00"
