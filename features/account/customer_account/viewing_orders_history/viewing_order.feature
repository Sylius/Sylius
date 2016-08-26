@customer_account
Feature: Viewing details of an order
    In order to check some details of my placed order
    As an Customer
    I want to be able to view details of my placed order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I bought a single "Angel T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And I chose "Free" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Viewing basic information about an order
        When I view the summary of the order "#00000666"
        And it should has number "#00000666"
        And I should see "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States" as shipping address
        And I should see "Mazikeen Lilim", "Pacific Coast Hwy", "90806", "Los Angeles", "United States" as billing address
        And I should see "$39.00" as order's total
