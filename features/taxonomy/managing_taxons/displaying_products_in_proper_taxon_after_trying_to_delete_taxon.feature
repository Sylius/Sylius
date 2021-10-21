@managing_taxons
Feature: Displaying products in proper taxon after trying to delete this taxon
    In order not to mess up the products to taxons assignments
    As an Administrator
    I want to keep the taxons structure the same after failed taxon deletion

    Background:
        Given the store operates on a channel named "Web"
        And the store classifies its products as "T-Shirts"
        And the "T-Shirts" taxon has children taxon "Men" and "Women"
        And the store has a product "T-Shirt Coconut" available in "Web" channel
        And this product belongs to "T-Shirts"
        And the store has a product "T-Shirt Banana" available in "Web" channel
        And this product belongs to "Men"
        And the product "T-Shirt Banana" has a main taxon "Men"
        And the store has a product "T-Shirt Apple" available in "Web" channel
        And this product belongs to "Men"
        And the store has a product "T-Shirt Pear" available in "Web" channel
        And this product belongs to "Women"
        And the store has a product "T-Shirt Watermelon" available in "Web" channel
        And this product belongs to "Women"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Displaying products in proper taxon after trying to delete this taxon
        When I try to delete taxon named "Men"
        Then I should be notified that I cannot delete a taxon in use
        And I should see in taxon "Men" in the store products "T-Shirt Banana" and "T-Shirt Apple"
        But I should not see in taxon "Men" in the store products "T-Shirt Pear" and "T-Shirt Watermelon"
