@viewing_products
Feature: Not seeing the lowest price for a product that has all taxons excluded on the channel
    In order to show the product's lowest price only in applicable taxons
    As a Guest
    I don't want to see the product's lowest price in taxons that have been excluded on the channel

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Category"
        And the "Category" taxon has children taxons "Clothes" and "PHP"
        And the "Clothes" taxon has child taxon "T-Shirts"
        And the store has a product "T-Shirt Banana" priced at "$21.00" belonging to the "T-Shirts" taxon
        And this product's price changed to "$10.00" and original price changed to "$21.00"
        And the store also has a product "T-Shirt Watermelon" priced at "$22.00" belonging to the "T-Shirts" taxon
        And the store also has a product "T-Shirt PHP" priced at "$23.00"
        And it belongs to "T-Shirts" and "PHP"
        And this product's price changed to "$15.00" and original price changed to "$23.00"

    @todo
    Scenario: Not seeing the lowest price for a product that has all taxons excluded on the channel
        Given the "T-Shirts" taxon is excluded from showing the lowest price of discounted products in the "United States" channel
        When I view product "T-Shirt Banana"
        Then I should not see information about its lowest price

    @todo
    Scenario: Not seeing the lowest price for a product that has parent taxon excluded on the channel
        Given the "Clothes" taxon is excluded from showing the lowest price of discounted products in the "United States" channel
        When I view product "T-Shirt Banana"
        Then I should not see information about its lowest price

    @todo
    Scenario: Not seeing the lowest price for a product that has root taxon excluded on the channel
        Given the "Category" taxon is excluded from showing the lowest price of discounted products in the "United States" channel
        When I view product "T-Shirt Banana"
        Then I should not see information about its lowest price

    @todo
    Scenario: Seeing the lowest price for a product that has not all taxons excluded on the channel
        Given the "T-Shirts" taxon is excluded from showing the lowest price of discounted products in the "United States" channel
        When I view product "T-Shirt PHP"
        Then I should see "$23.00" as its lowest price before the discount

    @todo
    Scenario: Not seeing the lowest price for a not discounted product that has all taxons excluded on the channel
        Given the "T-Shirts" taxon is excluded from showing the lowest price of discounted products in the "United States" channel
        When I view product "T-Shirt Watermelon"
        Then I should not see information about its lowest price
