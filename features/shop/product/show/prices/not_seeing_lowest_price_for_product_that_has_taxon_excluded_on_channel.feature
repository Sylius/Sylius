@viewing_products
Feature: Not seeing the lowest price for a product that has a taxon excluded on the channel
    In order to show the product's lowest price only in applicable taxons
    As a Guest
    I don't want to see the product's lowest price in taxons that have been excluded on the channel

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Category"
        And the "Category" taxon has children taxons "Groceries" and "Special offers"
        And the "Groceries" taxon has child taxon "Vegetables"
        And the store has a product "Broccoli" priced at "$20.00" belonging to the "Vegetables" taxon
        And this product's price changed to "$10.00" and original price changed to "$20.00"
        And the store also has a product "Cauliflower" priced at "$25.00"
        And it belongs to "Vegetables" and "Special offers"
        And this product's price changed to "$15.00" and original price changed to "$25.00"

    @api @ui
    Scenario: Not seeing the lowest price for a product that has a taxon excluded on the channel
        Given the "Vegetables" taxon is excluded from showing the lowest price of discounted products in the "United States" channel
        When I view product "Broccoli"
        Then I should not see information about its lowest price

    @api @ui
    Scenario: Not seeing the lowest price for a product that has parent taxon excluded on the channel
        Given the "Groceries" taxon is excluded from showing the lowest price of discounted products in the "United States" channel
        When I view product "Broccoli"
        Then I should not see information about its lowest price

    @api @ui
    Scenario: Not seeing the lowest price for a product that has root taxon excluded on the channel
        Given the "Category" taxon is excluded from showing the lowest price of discounted products in the "United States" channel
        When I view product "Broccoli"
        Then I should not see information about its lowest price

    @api @ui
    Scenario: Not seeing the lowest price for a product that has only one taxon excluded on the channel
        Given the "Vegetables" taxon is excluded from showing the lowest price of discounted products in the "United States" channel
        When I view product "Cauliflower"
        Then I should not see information about its lowest price
