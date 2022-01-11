@shopping_cart
Feature: Removing cart item from cart
    In order to delete some unnecessary cart items
    As a Visitor
    I want to be able to remove cart item

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt banana" priced at "$12.54"
        And I added product "T-shirt banana" to the cart

    @ui @api
    Scenario: Removing cart item
        When I see the summary of my cart
        And I remove product "T-shirt banana" from the cart
        Then my cart should be empty
        And my cart's total should be "$0.00"

    @ui @api
    Scenario: Removing cart item when the store has defined default shipping method
        Given the store has "UPS" shipping method with "$20.00" fee
        When I remove product "T-shirt banana" from the cart
        Then my cart should be empty
        And my cart's total should be "$0.00"

    @ui @api
    Scenario: Checking cart's total after removing one item
        Given the store has a product "T-shirt strawberry" priced at "$17.22"
        And I added product "T-shirt strawberry" to the cart
        When I remove product "T-shirt banana" from the cart
        Then my cart's total should be "$17.22"

    @ui @api
    Scenario: Removing cart item which causes order shipping method recalculation
        Given the store has "Paid" shipping category
        And the store has "Free" shipping category
        And product "T-shirt banana" belongs to "Paid" shipping category
        And the store has a product "T-shirt small" priced at "$15.00"
        And product "T-shirt small" belongs to "Free" shipping category
        And the store has "FedEx" shipping method with "$30.00" fee
        And this shipping method requires at least one unit matches to "Paid" shipping category
        And the store has "UPS" shipping method with "$0.00" fee
        And this shipping method requires that all units match to "Free" shipping category
        And I have product "T-shirt small" in the cart
        When I remove product "T-shirt banana" from the cart
        And I see the summary of my cart
        Then my cart shipping total should be "$0.00"
