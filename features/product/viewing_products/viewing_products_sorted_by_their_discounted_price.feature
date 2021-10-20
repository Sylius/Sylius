@viewing_products
Feature: Viewing products sorted by discounted price
    In order to see product variant sorted by discounted price
    As a Visitor
    I want to be able to see a properly sorted products by their discounted price

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Alcohol"
        And the store has a "Wyborowa Vodka" configurable product
        And this product belongs to "Alcohol"
        And the product "Wyborowa Vodka" has "Wyborowa Vodka Exquisite" variant priced at "$30.00"
        And the product "Wyborowa Vodka" has "Wyborowa Apple" variant priced at "$20.00"
        And the store has a "Jack Daniels Whiskey" configurable product
        And this product belongs to "Alcohol"
        And the product "Jack Daniels Whiskey" has "Jack Daniels Original" variant priced at "$25.00"
        And the product "Jack Daniels Whiskey" has "Jack Daniels Red" variant priced at "$40.00"
        And there is a catalog promotion "Worker Monday" that reduces price by "10%" and applies on "Alcohol" taxon

    @api @ui
    Scenario: Viewing a products with default variant's price sorted with discounted price
        When I browse products from taxon "Alcohol"
        And I sort products by the lowest price first
        Then the first product on the list should have name "Jack Daniels Whiskey"
        And the last product on the list should have name "Wyborowa Vodka"

