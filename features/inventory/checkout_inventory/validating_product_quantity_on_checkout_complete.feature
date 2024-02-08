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
        And the store ships everywhere for Free
        And the store allows paying Offline
        And I am a logged in customer

    @ui @api
    Scenario: Placing an order with products that have sufficient quantity
        When I add 3 products "Iron Maiden T-Shirt" to the cart
        And I proceed through checkout process
        And I confirm my order
        Then I should see the thank you page

    @ui @api
    Scenario: Being unable to place an order with product that is out of stock
        When I add 5 products "Iron Maiden T-Shirt" to the cart
        And I proceed through checkout process
        And other customer has bought 2 "Iron Maiden T-Shirt" products by this time
        And I confirm my order
        Then I should not see the thank you page
        And I should be notified that product "Iron Maiden T-Shirt" does not have sufficient stock

    @ui @api
    Scenario: Being unable to place an order with products that are out of stock
        When I add 5 products "Iron Maiden T-Shirt" to the cart
        And I add 5 products "2Pac T-Shirt" to the cart
        And I proceed selecting "Offline" payment method
        And other customer has bought 7 "2Pac T-Shirt" products by this time
        And I confirm my order
        Then I should not see the thank you page
        And I should be notified that product "2Pac T-Shirt" does not have sufficient stock
