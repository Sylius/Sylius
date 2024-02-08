@managing_orders
Feature: Sending a confirmation email after shipping an order
    In order to let the customer know when the order gets shipped
    As an Administrator
    I want to have the confirmation email sent after shipping an order

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store has a product "Angel T-Shirt"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui @email
    Scenario: Sending a confirmation email after shipping an order
        When I view the summary of the order "#00000666"
        And specify its tracking code as "#00044"
        And I ship this order
        Then an email with shipment's details of this order should be sent to "lucy@teamlucifer.com"

    @ui @email
    Scenario: Sending a confirmation email after shipping an order in different locale than the default one
        Given the order "#00000666" has been placed in "Polish (Poland)" locale
        When I view the summary of the order "#00000666"
        And specify its tracking code as "#00044"
        And I ship this order
        Then an email with shipment's details of this order should be sent to "lucy@teamlucifer.com" in "Polish (Poland)" locale
