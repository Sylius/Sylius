@managing_inventory
Feature: Seeing product's variant with specify quantity of items on hand
    In order to see how many variants of selected product are available on hand
    As an Administrator
    I want to be able to see the on hand quantity of a selected product variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-shirt banana" configurable product
        And the product "T-shirt banana" has "Yellow" variant priced at "€20.54"
        And the product "T-shirt banana" has "Green" variant priced at "€5.54"
        And there are 5 units of "Yellow" variant of product "T-shirt banana" available in the inventory
        And there are 5 units of "Green" variant of product "T-shirt banana" available in the inventory
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing decreased quantity of product's items in selected variant after order payment
        Given there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought 3 units of "Green" variant of product "T-shirt banana"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And this order is already paid
        When I view all variants of the product "T-shirt banana"
        Then the variant "Green" should have 2 items on hand

    @ui
    Scenario: Seeing decreased quantity of product's items from different variants after order payment
        Given there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought 3 units of "Yellow" variant of product "T-shirt banana"
        And the customer bought 2 units of "Green" variant of product "T-shirt banana"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And this order is already paid
        When I view all variants of the product "T-shirt banana"
        Then the variant "Yellow" should have 2 items on hand
        And the variant "Green" should have 3 items on hand
