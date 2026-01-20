@receiving_discount
Feature: Receiving fixed discount distributed correctly on item units
    In order to pay proper amount while buying promoted goods
    As a Visitor
    I want to have fixed discounts applied correctly across item units

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$10.56"
        And the store has a product "Symfony Mug" priced at "$60.00"
        And there is a promotion "Small Order Discount" with priority 0
        And it gives "$20.00" discount to every order
        And there is a promotion "Large Order Discount" with priority 1
        And it gives "$80.00" discount to every order
        And the store has "DHL" shipping method with "$1.26" fee

    @ui @api @mink:chromedriver
    Scenario: Applying stacked fixed discounts on multiple item units
        When I add 3 products "PHP T-Shirt" to the cart
        And I add 1 products "Symfony Mug" to the cart
        Then my cart total should be "$1.26"
        And my discount should be "-$91.68"
        And my cart shipping total should be "$1.26"
