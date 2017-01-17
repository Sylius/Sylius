@applying_taxes
Feature: Apply different taxes for variants with different tax category
    In order to pay proper amount when buying goods with variants from different tax categories
    As a Visitor
    I want to have correct taxes applied to my order

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store has "US VAT" tax rate of 23% for "Mugs" within the "US" zone
        And the store has "Low VAT" tax rate of 5% for "Cheap Mugs" within the "US" zone
        And the store has a product "PHP Mug"
        And it comes in the following variations:
            | name       | price   |
            | Medium Mug | $100.00 |
            | Large Mug  | $40.00  |
        And "Medium Mug" variant of product "PHP Mug" belongs to "Cheap Mugs" tax category
        And "Large Mug" variant of product "PHP Mug" belongs to "Mugs" tax category

    @ui
    Scenario: Proper taxes for different taxed variants
        When I add "Medium Mug" variant of product "PHP Mug" to the cart
        And I add "Large Mug" variant of product "PHP Mug" to the cart
        Then my cart total should be "$154.20"
        And my cart taxes should be "$14.20"
