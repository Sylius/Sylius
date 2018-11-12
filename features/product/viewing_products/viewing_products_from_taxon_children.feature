@viewing_products
Feature: Viewing products from taxon children
    In order to browse products from general taxons
    As a Visitor
    I want to be able to view products from taxon children

    Background:
        Given the store has currency "Euro"
        And the store operates on a channel named "Poland"
        And the store classifies its products as "T-Shirts"
        And the "T-Shirts" taxon has children taxon "Men" and "Women"
        And the "Men" taxon has children taxon "XL" and "XXL"
        And the store has a product "T-Shirt Banana" available in "Poland" channel
        And this product belongs to "T-Shirts"
        And this product has "Atan banana", "Goldfinger banana" and "Lacatan banana" variants
        And the store has a product "T-Shirt Banana For Men" available in "Poland" channel
        And this product belongs to "Men"
        And this product has "Chingan banana", "Sinwobogi banana" and "Señorita banana" variants
        And the store has a product "T-Shirt Banana For Men XXL" available in "Poland" channel
        And this product belongs to "XXL"
        And this product has "Gros Michel banana", "Robusta" and "Flhorban 920" variants
        And the store has a product "T-Shirt Pineapple" available in "Poland" channel
        And this product belongs to "T-Shirts"
        And this product has "Abacaxi", "Tropical Gold" and "Victoria" variants
        And the store has a product "T-Shirt Pineapple For Men" available in "Poland" channel
        And this product belongs to "Men"
        And this product has "Red Pineapple", "Cayenne" and "Hilo" variants
        And the store has a product "T-Shirt Pineapple For Men XXL" available in "Poland" channel
        And this product belongs to "XXL"
        And this product has "Sarawak", "Bumanguesa" and "Kona Sugarloaf" variants
        And the store has a product "T-Shirt Tomato" available in "Poland" channel
        And this product belongs to "T-Shirts"
        And this product has "Black Cherry", "Sunrise Bumble Bee" and "Sungold" variants
        And the store has a product "T-Shirt Tomato For Men" available in "Poland" channel
        And this product belongs to "Men"
        And this product has "Sweet 100", "Pantano Romanesco" and "Green Zebra" variants
        And the store has a product "T-Shirt Tomato For Men XXL" available in "Poland" channel
        And this product belongs to "XXL"
        And this product has "Black Zebra", "Costoluto Genovese" and "Sweet Clusters" variants

    @ui
    Scenario: Viewing products from taxon children
        When I browse products from taxon "T-Shirts"
        Then I should see the product "T-Shirt Banana"
        And I should see the product "T-Shirt Banana For Men"
        And I should see the product "T-Shirt Banana For Men XXL"
        And I should see 9 products in the list
