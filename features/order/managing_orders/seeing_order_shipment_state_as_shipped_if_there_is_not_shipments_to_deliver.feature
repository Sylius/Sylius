@managing_orders
Feature: Seeing shipping states of an order as shipped if there are no shipments to deliver
    In order to have coherent shipping states of all orders
    As an Administrator
    I want orders with no undelivered shipments to have shipping state shipped

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Guards! Guards!" configurable product
        And this product has "Guards! Guards! - ebook" variant priced at "$12.55" which does not require shipping
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Guards! Guards! - ebook" variant of product "Guards! Guards!"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing shipping state as shipped on orders list
        When I browse orders
        Then the order "#00000666" should have order shipping state "Shipped"

    @ui
    Scenario: Seeing shipping state as shipped on order's summary
        When I view the summary of the order "#00000666"
        Then it should have order's shipping state "Shipped"
        And I should not see information about shipments
