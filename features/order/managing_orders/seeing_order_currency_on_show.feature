@managing_orders
Feature: Seeing the currency an order has been placed in on it's details page
    In order to be aware what currency a specific order has been placed in
    As an Administrator
    I want to see every price in order's currency

    Background:
        Given the store ships to "British Virgin Islands"
        And the store has a zone "English" with code "EN"
        And this zone has the "British Virgin Islands" country member
        And the store operates on a channel named "Web" in "USD" currency
        And that channel allows to shop using "USD" and "GBP" currencies
        And the store has "Low VAT" tax rate of 10% for "Lowered EN services" within the "EN" zone
        And the store allows paying with "Cash on Delivery"
        And the store has "DHL" shipping method with "$20.00" fee within the "EN" zone
        And the store has a product "Angel T-Shirt" priced at "$20.00"
        And it belongs to "Lowered EN services" tax category
        And there is a promotion "Order's Extravaganza"
        And this promotion gives "$5.00" discount to every order
        And there is a promotion "Fabulous Garishness"
        And this promotion gives "$10.00" off on every product with minimum price at "$15.00"
        And there is a customer "Satin" identified by an email "satin@teamlucifer.com" and a password "pswd"
        And I am logged in as an administrator

    @ui
    Scenario: All prices are in the base currency when the client haven't chosen any other
        Given there is a customer "satin@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And the customer "No Face" addressed it to "Lucifer Morningstar", "Seaside Fwy" "90802" in the "British Virgin Islands"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "British Virgin Islands"
        And the customer chose "DHL" shipping method with "Cash on Delivery" payment
        When I view the summary of the order "#00000666"
        And I check "Angel T-Shirt" data
        Then its discounted unit price should be $10.00
        And its unit price should be $20.00
        And its subtotal should be $10.00
        And its discount should be -$5.00
        And its tax should be $0.50
        And its total should be $5.50
        And the order's items total should be "$5.50"
        And the order's shipping total should be "$20.00"
        And the order's tax total should be "$0.50"
        And the order's promotion total should be "-$5.00"
        And the order's total should be "$25.50"
