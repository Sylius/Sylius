@managing_catalog_promotions
Feature: Filtering catalog promotions by channel
    In order to see catalog promotions of a specific channel
    As an Administrator
    I want to be able to filter catalog promotions on the list

    Background:
        Given the store operates on a channel named "Web-EU"
        And the store also operates on a channel named "Web-US"
        And the store classifies its products as "Clothes", "Shirts" and "Dishes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$100.00"
        And there is a catalog promotion "Winter sale" between "2021-12-20" and "2021-12-30" available in "Web-US" channel that reduces price by "30%" and applies on "Clothes" taxon
        And there is another catalog promotion "Spring sale" between "2022-04-01" and "2022-05-01" available in "Web-EU" channel that reduces price by "25%" and applies on "Shirts" taxon
        And there is disabled catalog promotion "Surprise sale" between "2021-07-01" and "2022-05-04" available in "Web-US" channel that reduces price by "90%" and applies on "Dishes" taxon
        And this catalog promotion is also available in the "Web-EU" channel
        And I am logged in as an administrator

    @ui @api
    Scenario: Filtering catalog promotions by a chosen channel
        When I browse catalog promotions
        And I filter by "Web-EU" channel
        Then I should see a catalog promotion with name "Surprise sale"
        And I should see a catalog promotion with name "Spring sale"
        But I should not see a catalog promotion with name "Winter sale"
