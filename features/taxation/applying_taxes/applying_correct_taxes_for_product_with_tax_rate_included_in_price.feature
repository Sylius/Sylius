@applying_taxes
Feature: Applying correct taxes for items with tax rate included in price
    In order to pay proper amount when buying goods with tax rate included in price
    As a Visitor
    I want to have correct taxes applied to my order

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store has included in price "VAT" tax rate of 20% for "Clothes" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$19.70"
        And it belongs to "Clothes" tax category
        And the store has a product "Symfony T-Shirt" priced at "$19.70"
        And it belongs to "Clothes" tax category

    @ui @api
    Scenario: Applying correct taxes for a single item with tax rate included in price
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$19.70"
        And my included in price taxes should be "$3.28"
        And there should be one item in my cart
        And total price of "PHP T-Shirt" item should be "$19.70"

    @ui @api
    Scenario: Applying correct taxes for a single item with multiple units with tax rate included in price
        When I add 2 products "PHP T-Shirt" to the cart
        Then my cart total should be "$39.40"
        And my included in price taxes should be "$6.57"
        And total price of "PHP T-Shirt" item should be "$39.40"

    @ui @api
    Scenario: Applying correct taxes for multiple items with tax rate included in price and default calculator
        When I add products "PHP T-Shirt" and "Symfony T-Shirt" to the cart
        Then my cart total should be "$39.40"
        And my included in price taxes should be "$6.56"

    @ui @api
    Scenario: Applying correct taxes for multiple items with tax rate included in price and decimal calculator
        Given the "VAT" tax rate has decimal calculator configured
        When I add products "PHP T-Shirt" and "Symfony T-Shirt" to the cart
        Then my cart total should be "$39.40"
        And my included in price taxes should be "$6.57"
