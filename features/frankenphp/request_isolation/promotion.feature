@worker @worker:promotion @mink:workerphp
Feature: Promotion state isolation in FrankenPHP worker mode

  Verify promotion system state isolation between requests.
  Tests: carts with & without promotions in same worker process.

  Background:
    Given the store operates on a channel named "Web"
    And the store has a product "T-Shirt" priced at "$100.00"
    And the store has a product "Cap" priced at "$30.00"

  @ui
  Scenario: Request 1 - Setup promotion and add to cart
    Given there is a promotion "Summer Sale"
    And the promotion gives "$50.00" discount to every order
    And I have product "T-Shirt" in the cart
    When I check the details of my cart
    Then my cart total should be "$50.00"

  @ui
  Scenario: Request 2 - Fresh cart - verify state isolation
    # Same worker process, SAME singleton instances!
    # Database cleaned by hook.doctrine_orm between scenarios
    # If state leaked from R1, discount would still apply (state leak bug!)
    Given I have product "T-Shirt" in the cart
    When I check the details of my cart
    Then my cart total should be "$100.00"

  @ui
  Scenario: Item total rule checker - qualifies then fresh cart does not qualify
    Given there is a promotion "Big Spender"
    And the promotion gives "$40.00" discount to every order with items total at least "$100.00"
    And I have product "T-Shirt" in the cart
    When I check the details of my cart
    Then my cart total should be "$60.00"

  @ui
  Scenario: Item total rule checker - order below threshold does not get discount after previous scenario
    Given there is a promotion "Big Spender"
    And the promotion gives "$40.00" discount to every order with items total at least "$100.00"
    And I have product "Cap" in the cart
    When I check the details of my cart
    Then my cart total should be "$30.00"

  @ui
  Scenario: Cart quantity rule checker - qualifies then order below threshold does not qualify
    Given there is a promotion "Buy Two Discount"
    And the promotion gives "$20.00" discount to every order with quantity at least 2
    And I have 2 products "T-Shirt" in the cart
    When I check the details of my cart
    Then my cart total should be "$180.00"

  @ui
  Scenario: Cart quantity rule checker - order with quantity 1 does not get discount
    Given there is a promotion "Buy Two Discount"
    And the promotion gives "$20.00" discount to every order with quantity at least 2
    And I have 1 product "T-Shirt" in the cart
    When I check the details of my cart
    Then my cart total should be "$100.00"

  @ui
  Scenario: Coupon promotion applied to cart
    Given the store has promotion "Holiday Sale" with coupon "SANTA50"
    And this promotion gives "$50.00" discount to every order
    And this coupon can be used 1 times
    And I have product "T-Shirt" in the cart
    And I applied the coupon with code "SANTA50"
    When I check the details of my cart
    Then my cart total should be "$50.00"

  @ui
  Scenario: Fresh cart in subsequent request does not retain coupon discount
    Given I have product "T-Shirt" in the cart
    When I check the details of my cart
    Then my cart total should be "$100.00"
