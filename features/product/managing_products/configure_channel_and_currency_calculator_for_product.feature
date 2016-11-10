@managing_products
Feature: Configure channel and currency calculator
    In order to define products price per channel and currency
    As an Administrator
    I want to add a new product to the shop with different prices

  Background:
      Given the store is available in "English (United States)"
      And the store operates on a channel named "Web"
      And I am logged in as an administrator

  @ui @javascript
  Scenario: Configure channel and currency calculator while adding new simple product
      Given I want to create a new simple product
      When I specify its code as "BOARD_DICE_BREWING"
      And I name it "Dice Brewing" in "English (United States)"
      And I choose "Channel and currency" calculator
      And I set its price to "$20.00"
      And I set its price to "$10.00" for "USD" currency and "Web" channel
      And I set its slug to "dice-brewing"
      And I add it
      Then I should be notified that it has been successfully created
      And the product "Dice Brewing" should appear in the shop
