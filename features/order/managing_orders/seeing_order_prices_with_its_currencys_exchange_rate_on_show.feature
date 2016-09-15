@managing_orders
Feature: Seeing all prices calculated accordingly to it's currency's exchange rate on details page
    In order to be aware what was the exchange rate of order's currency at the time of placing
    As an Administrator
    I want to see every price calculated accordingly to the exchange rate of order's currency when it was placed

    Background:
        Given the store ships to "British Virgin Islands"
        And the store has a zone "English" with code "EN"
        And this zone has the "British Virgin Islands" country member
        And the store operates on a channel named "Web"
        And that channel allows to shop using the "USD" currency
        And that channel allows to shop using the "GBP" currency with exchange rate 2.00
        And that channel uses the "USD" currency by default
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
    Scenario: All of a placed order prices are in base currency's exchange rate
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

    @ui
    Scenario: All of a placed order prices don't change when the base currency's exchange rate changes
        Given there is a customer "satin@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And the customer "No Face" addressed it to "Lucifer Morningstar", "Seaside Fwy" "90802" in the "British Virgin Islands"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "British Virgin Islands"
        And the customer chose "DHL" shipping method with "Cash on Delivery" payment
        But the exchange rate for currency "USD" was changed to 2.00
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

    @ui
    Scenario: All of a placed order's prices are in the currency's chosen by the customer and it's exchange rate
        Given there is a customer "satin@teamlucifer.com" that placed an order "#00000666"
        And the customer has chosen to order in the "GBP" currency
        And the customer bought a single "Angel T-Shirt"
        And the customer "No Face" addressed it to "Lucifer Morningstar", "Seaside Fwy" "90802" in the "British Virgin Islands"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "British Virgin Islands"
        And the customer chose "DHL" shipping method with "Cash on Delivery" payment
        When I view the summary of the order "#00000666"
        And I check "Angel T-Shirt" data
        Then its discounted unit price should be £20.00
        And its unit price should be £40.00
        And its subtotal should be £20.00
        And its discount should be -£10.00
        And its tax should be £1.00
        And its total should be £11.00
        And the order's items total should be "£11.00"
        And the order's shipping total should be "£40.00"
        And the order's tax total should be "£1.00"
        And the order's promotion total should be "-£10.00"
        And the order's total should be "£51.00"

    @ui
    Scenario: All of a placed order's prices are in the currency's chosen by the customer and it's exchange rate at the time of placing
        Given there is a customer "satin@teamlucifer.com" that placed an order "#00000666"
        And the customer has chosen to order in the "GBP" currency
        And the customer bought a single "Angel T-Shirt"
        And the customer "No Face" addressed it to "Lucifer Morningstar", "Seaside Fwy" "90802" in the "British Virgin Islands"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "British Virgin Islands"
        And the customer chose "DHL" shipping method with "Cash on Delivery" payment
        But the exchange rate for currency "GBP" was changed to 3.00
        When I view the summary of the order "#00000666"
        And I check "Angel T-Shirt" data
        Then its discounted unit price should be £20.00
        And its unit price should be £40.00
        And its subtotal should be £20.00
        And its discount should be -£10.00
        And its tax should be £1.00
        And its total should be £11.00
        And the order's items total should be "£11.00"
        And the order's shipping total should be "£40.00"
        And the order's tax total should be "£1.00"
        And the order's promotion total should be "-£10.00"
        And the order's total should be "£51.00"
