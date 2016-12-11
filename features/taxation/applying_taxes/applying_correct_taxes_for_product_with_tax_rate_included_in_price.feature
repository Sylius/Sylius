@applying_taxes
Feature: Apply correct taxes for items with tax rate included in price
    In order to pay proper amount when buying goods with tax rate included in price
    As a Visitor
    I want to have correct taxes applied to my order

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store has included in price "VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "Clothes" tax category

    @ui
    Scenario: Proper taxes for taxed product
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$100.00"
        And my cart taxes should be "$18.70"
        And there should be one item in my cart
        And total price of "PHP T-Shirt" item should be "$100.00"
