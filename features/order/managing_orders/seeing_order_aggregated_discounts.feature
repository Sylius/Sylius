@managing_orders
Feature: Seeing aggregated discounts of an order
    In order to be aware of discounts applied to an order
    As an Administrator
    I want to see aggregated discount of a specific order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Longbow" priced at "$150.00"
        And the store has a product "Bastard sword" priced at "$200.00"
        And the store has "DHL" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And there is a promotion "Eagle eye promotion"
        And it gives "$20.00" discount to every order
        And it gives "50%" discount on shipping to every order
        And there is a customer "robin.hood@sherwood.com" that placed an order "#00000006"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing shipping and order promotions, but the shipping promotion is not aggregated in summary's promotion total
        Given the customer bought 2 "Longbow" products
        And the customer chose "DHL" shipping method to "United States" with "Cash on Delivery" payment
        When I view the summary of the order "#00000006"
        Then the order's items total should be "$280.00"
        And the order's shipping promotion should be "Eagle eye promotion -$5.00"
        And the order's promotion discount should be "Eagle eye promotion -$20.00"
        And the order's promotion total should be "-$20.00"
        And there should be a shipping charge "DHL $10.00"
        And the order's shipping total should be "$5.00"
        And the order's total should be "$285.00"

    @ui
    Scenario: Seeing multiple order promotions aggregated in summary
        Given there is a promotion "Big order discount"
        And it gives "$50.00" discount to every order with quantity at least 3
        And the customer bought 2 "Longbow" products
        And the customer bought 3 "Bastard sword" products
        And the customer chose "DHL" shipping method to "United States" with "Cash on Delivery" payment
        When I view the summary of the order "#00000006"
        Then the order's items total should be "$830.00"
        And the order's shipping promotion should be "Eagle eye promotion -$5.00"
        And the order's promotion discount should be "Eagle eye promotion -$20.00"
        And the order's promotion discount should be "Big order discount -$50.00"
        And the order's promotion total should be "-$70.00"
        And there should be a shipping charge "DHL $10.00"
        And the order's shipping total should be "$5.00"
        And the order's total should be "$835.00"
