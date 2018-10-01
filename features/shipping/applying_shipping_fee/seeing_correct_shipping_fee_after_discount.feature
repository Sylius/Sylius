@receiving_discount
Feature: Receiving percentage discount on shipping
  In order to pay decreased amount for shipping
  As a Customer
  I want to see the correct fees for the shipping methods available

  Background:
    Given the store operates on a single channel in "United States"
    And the store has "DHL" shipping method with "$10.00" fee
    And the store has a product "PHP T-Shirt" priced at "$100.00"
    And there is a promotion "Holiday promotion"
    And I am a logged in customer

  @ui
  Scenario: Receiving percentage discount on shipping
    Given the promotion gives "20%" discount on shipping to every order
    When I add product "PHP T-Shirt" to the cart
    And I am at the checkout addressing step
    And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
    And I complete the addressing step
    Then I should see shipping method "DHL" with fee "$8.00"

  @ui
  Scenario: Receiving free shipping
    Given the promotion gives free shipping to every order
    When I add product "PHP T-Shirt" to the cart
    And I am at the checkout addressing step
    And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
    And I complete the addressing step
    Then I should see shipping method "DHL" with fee "$0.00"
