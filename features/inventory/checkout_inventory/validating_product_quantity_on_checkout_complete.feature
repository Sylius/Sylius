@checkout_inventory
Feature: Being unable to buy products that are out of stock
    In order to be sure that products I buy are available
    As a Customer
    I want to be prevented from placing an order with products that are out of stock

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Iron Maiden T-Shirt" priced at "€12.54"
        And the store also has a product "2Pac T-Shirt" priced at "€13.24"
        And "Iron Maiden T-Shirt" product is tracked by the inventory
        And "2Pac T-Shirt" product is also tracked by the inventory
        And there are 5 units of product "Iron Maiden T-Shirt" available in the inventory
        And there are 10 units of product "2Pac T-Shirt" available in the inventory
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Placing an order with products that have sufficient quantity
        Given I have added 3 products "Iron Maiden T-Shirt" to the cart
        And I have proceeded selecting "Offline" payment method
        When I confirm my order
        Then I should see the thank you page

    @ui
    Scenario: Being unable to place an order with product that is out of stock
        Given I have added 5 products "Iron Maiden T-Shirt" to the cart
        And I have proceeded selecting "Offline" payment method
        When other customer has bought 2 "Iron Maiden T-Shirt" products by this time
        And I confirm my order
        Then I should not see the thank you page
        And I should be notified that this product does not have sufficient stock

    @ui
    Scenario: Being unable to place an order with products that are out of stock
        Given I have added 5 products "Iron Maiden T-Shirt" to the cart
        And I have added 5 products "2Pac T-Shirt" to the cart
        And I have proceeded selecting "Offline" payment method
        When other customer has bought 7 "2Pac T-Shirt" products by this time
        And I confirm my order
        Then I should not see the thank you page
        And I should be notified that this product does not have sufficient stock
