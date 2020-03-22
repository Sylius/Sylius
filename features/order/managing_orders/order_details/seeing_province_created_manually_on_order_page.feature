@managing_orders
Feature: Seeing province created manually on order summary page
    In order to be certain about province which was created manually by customer
    As a Administrator
    I want to be able to see province on the order summary page

    Background:
        Given the store operates on a channel named "Web"
        And the store operates in "United Kingdom"
        And the store has a zone "English" with code "EN"
        And this zone has the "United Kingdom" country member
        And the store allows paying with "Cash on Delivery"
        And the store has "DHL" shipping method with "$20.00" fee within the "EN" zone
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Norfolk" in the "United Kingdom", "East of England"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Peterborough", "United Kingdom", "East of England"
        And the customer chose "DHL" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing manually definied province on order summary page
        When I view the summary of the order "#00000666"
        Then I should see "East of England" as province in the shipping address
        And I should see "East of England" ad province in the billing address
