@managing_inventory
Feature: Inventory releasing on order cancellation
    In order to be certain that reserved inventory is back to stock correctly
    As an Administrator
    I want to be able to see the correct quantity of a specific product variant in stock after order cancellation

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana"
        And the product "T-shirt banana" has "Green" variant priced at "€5.54"
        And the product "T-shirt banana" has "Red" variant priced at "€5.54"
        And there are 5 units of "Green" variant of product "T-shirt banana" available in the inventory
        And there are 5 units of "Red" variant of product "T-shirt banana" available in the inventory
        And the store has a product "Skirt watermelon"
        And the product "Skirt watermelon" has "Yellow" variant priced at "€500.43"
        And there are 5 units of "Yellow" variant of product "Skirt watermelon" available in the inventory
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am logged in as an administrator

    @ui
    Scenario: Verify the reserved inventory is back in stock after cancellation of a new order
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought 3 units of "Green" variant of product "T-shirt banana"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And the order "#00000022" was cancelled
        When I view all variants of the product "T-shirt banana"
        Then the variant "Green" should have 5 items on hand
        And the "Green" variant should have 0 items on hold

    @ui
    Scenario: Verify the reserved inventory and quantity of product's items is back in stock after cancellation of paid order
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought 3 units of "Green" variant of product "T-shirt banana"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And the order "#00000022" is already paid
        And the order "#00000022" was cancelled
        When I view all variants of the product "T-shirt banana"
        Then the variant "Green" should have 5 items on hand
        And the "Green" variant should have 0 items on hold

    @ui
    Scenario: Verify the reserved inventory is back in stock after cancellation of a new order with two variants of product
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000023"
        And the customer bought 3 units of "Green" variant of product "T-shirt banana"
        And the customer bought 2 units of "Red" variant of product "T-shirt banana"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And the order "#00000023" was cancelled
        When I view all variants of the product "T-shirt banana"
        Then the variant "Green" should have 5 items on hand
        And the "Green" variant should have 0 items on hold
        And the variant "Red" should have 5 items on hand
        And the "Red" variant should have 0 items on hold

    @ui
    Scenario: Verify the reserved inventory and quantity of product's items is back in stock after cancellation of paid order with two variants of product
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000023"
        And the customer bought 3 units of "Green" variant of product "T-shirt banana"
        And the customer bought 2 units of "Red" variant of product "T-shirt banana"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And the order "#00000023" is already paid
        And the order "#00000023" was cancelled
        When I view all variants of the product "T-shirt banana"
        Then the variant "Green" should have 5 items on hand
        And the "Green" variant should have 0 items on hold
        And the variant "Red" should have 5 items on hand
        And the "Red" variant should have 0 items on hold

    @ui
    Scenario: Verify the reserved inventory is back in stock after cancellation of a new order with two variants of different products
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000024"
        And the customer bought 3 units of "Green" variant of product "T-shirt banana"
        And the customer bought 2 units of "Yellow" variant of product "Skirt watermelon"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And the order "#00000024" was cancelled
        Then the "Green" variant of "T-shirt banana" product should have 5 items on hand
        And the "Green" variant of "T-shirt banana" product should have 0 items on hold
        And the "Yellow" variant of "Skirt watermelon" product should have 5 items on hand
        And the "Yellow" variant of "Skirt watermelon" product should have 0 items on hold

    @ui
    Scenario: Verify the reserved inventory and quantity of product's items is back in stock after cancellation of paid order with two variants of different products
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000024"
        And the customer bought 3 units of "Green" variant of product "T-shirt banana"
        And the customer bought 2 units of "Yellow" variant of product "Skirt watermelon"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And the order "#00000024" is already paid
        And the order "#00000024" was cancelled
        Then the "Green" variant of "T-shirt banana" product should have 5 items on hand
        And the "Green" variant of "T-shirt banana" product should have 0 items on hold
        And the "Yellow" variant of "Skirt watermelon" product should have 5 items on hand
        And the "Yellow" variant of "Skirt watermelon" product should have 0 items on hold

    @ui
    Scenario: Verify the reserved inventory and quantity of product's items is back in stock after cancellation of a refunded order
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought 3 units of "Green" variant of product "T-shirt banana"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And the order "#00000022" is already paid
        But this order has been refunded
        And the order "#00000022" was cancelled
        When I view all variants of the product "T-shirt banana"
        Then the variant "Green" should have 5 items on hand
        And the "Green" variant should have 0 items on hold
