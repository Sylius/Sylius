@customer_account
Feature: Viewing order items with proper names
    In order to check some details of my placed order
    As an Customer
    I want to be able to view items with proper names of my placed order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And the store has a product "Angel Mug" priced at "$19.00"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I bought an "Angel T-Shirt" and an "Angel Mug"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And I chose "Free" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Viewing basic information about an order
        Given the product "Angel T-Shirt" was renamed to "Devil Cardigan"
        And the product "Angel Mug" was renamed to "Devil Glass"
        When I view the summary of the order "#00000666"
        And I should see 2 items in the list
        And the product named "Angel T-Shirt" should be in the items list
        And the product named "Angel Mug" should be in the items list
