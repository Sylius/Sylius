@inventory
Feature: Seeing product's variant with specify quantity of items on hand
    In order to see how many variants of selected product are available on hand
    As an Administrator
    I want to be able to see quantity of variants available on hand for a selected product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt banana" priced at "€12.54"
        And the product "T-shirt banana" has "With Potato" variant priced at "€20.54"
        And the product "T-shirt banana" has "With Watermelon" variant priced at "€5.54"
        And there are 5 items of product "T-shirt banana" in variant "With Potato" available in the inventory
        And there are 5 items of product "T-shirt banana" in variant "With Watermelon" available in the inventory
        And the store has a product "Skirt Sheep" priced at "€34.54"
        And the product "Skirt Sheep" has "Red" variant priced at "€5.54"
        And there are 5 items of product "Skirt Sheep" in variant "Red" available in the inventory
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing decreased quantity of product's items in selected variant after order payment
        Given there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought 3 "Skirt Sheep" products in variant "Red"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And this order is already paid
        When I view all variants of the product "Skirt Sheep"
        Then I should see 2 variant in the list
        And the variant "Red" should have 2 items on hand

    @ui
    Scenario: Seeing decreased quantity of product's items from different variants after order payment
        Given there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought 3 "T-shirt banana" products in variant "With Potato"
        And the customer bought 2 "T-shirt banana" products in variant "With Watermelon"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And this order is already paid
        When I view all variants of the product "T-shirt banana"
        Then I should see 3 variant in the list
        And the variant "With Potato" should have 2 items on hand
        And the variant "With Watermelon" should have 3 items on hand
