@customer_account
Feature: Seeing province created manually in order history
    In order to be certain about province which I created manually
    As a Customer
    I want to be able to see province in the order history

    Background:
        Given the store operates on a channel named "Web" in "USD" currency
        And the store operates in "United Kingdom"
        And the store has a zone "English" with code "EN"
        And this zone has the "United Kingdom" country member
        And the store allows paying with "Cash on Delivery"
        And the store has "DHL" shipping method with "$20.00" fee within the "EN" zone
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I bought a single "Angel T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Norfolk" in the "United Kingdom", "East of England"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Peterborough", "United Kingdom", "East of England"
        And I chose "DHL" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Seeing a province manually defined in a order history
        When I view the summary of the order "#00000666"
        Then I should see "East of England" as province in the shipping address
        And I should see "East of England" as province in the billing address
