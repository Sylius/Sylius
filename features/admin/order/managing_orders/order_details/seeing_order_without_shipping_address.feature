@managing_orders
Feature: Seeing an order without shipping address
    In order to see details of a specific order which does not have to be shipped
    As an Administrator
    I want to be able to view basic information about an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel E-book"
        And this product does not require shipping
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel E-book"
        And the customer set the billing address as "Mike Ross", "350 5th Ave", "10118", "New York", "United States"
        And the customer chose "Cash on Delivery" payment
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing basic information about an order
        When I view the summary of the order "#00000666"
        Then it should have been placed by the customer "lucy@teamlucifer.com"
        And it should have "Mike Ross", "350 5th Ave", "10118", "New York", "United States" as its billing address
        And it should be paid with "Cash on Delivery"
        And it should have no shipping address set
