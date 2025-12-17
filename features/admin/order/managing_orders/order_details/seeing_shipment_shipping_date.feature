@managing_orders
Feature: Seeing shipment shipping date
    In order to get to know when shipment has been shipped
    As an Administrator
    I want to be able to see shipment shipping date

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Gryffindor scarf" priced at "$100.00"
        And the store has "Owl post" shipping method with "$10.00" fee within the "US" zone
        And the store allows paying Offline
        And there is a customer "fleur@delacour.com" that placed an order "#00000777"
        And the customer bought a single "Gryffindor scarf"
        And the customer chose "Owl post" shipping method to "United States" with "Offline" payment
        And it is "20-02-2020 10:30:05" now
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing shipped at date
        When I view the summary of the order "#00000777"
        And I ship this order
        Then I should see the shipping date as "20-02-2020 10:30:05"
