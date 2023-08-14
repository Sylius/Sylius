@managing_products
Feature: Sorting listed products from a taxon by position
    In order to change the order by which products from a taxon are displayed
    As an Administrator
    I want to sort products from a taxon by their positions

    Background:
        Given the store classifies its products as "Soft Toys"
        And the store has a product "Old pug"
        And this product is in "Soft Toys" taxon at 1st position
        And the store has a product "Young pug"
        And this product is in "Soft Toys" taxon at 2nd position
        And the store has a product "Small pug"
        And this product is in "Soft Toys" taxon at 3rd position
        And the store has a product "Tiny pug"
        And this product is in "Soft Toys" taxon at 4th position
        And the store has a product "Large pug"
        And this product is in "Soft Toys" taxon at 5th position
        And the store has a product "Medium pug"
        And this product is in "Soft Toys" taxon at 6th position
        And the store has a product "Giant pug"
        And this product is in "Soft Toys" taxon at 7th position
        And the store has a product "Mini pug"
        And this product is in "Soft Toys" taxon at 8th position
        And the store has a product "Nano pug"
        And this product is in "Soft Toys" taxon at 9th position
        And the store has a product "Micro pug"
        And this product is in "Soft Toys" taxon at 10th position
        And the store has a product "Puglet"
        And this product is in "Soft Toys" taxon at 11th position
        And the store has a product "Pug XL"
        And this product is in "Soft Toys" taxon at 12th position
        And the store has a product "Pug XS"
        And this product is in "Soft Toys" taxon at 13th position
        And the store has a product "Puggle"
        And this product is in "Soft Toys" taxon at 14th position
        And the store has a product "Pug Junior"
        And this product is in "Soft Toys" taxon at 15th position
        And the store has a product "Pug Senior"
        And this product is in "Soft Toys" taxon at 16th position
        And the store has a product "Pug Master"
        And this product is in "Soft Toys" taxon at 17th position
        And the store has a product "Pug Pro"
        And this product is in "Soft Toys" taxon at 18th position
        And the store has a product "Pug Expert"
        And this product is in "Soft Toys" taxon at 19th position
        And the store has a product "Ultimate Pug"
        And this product is in "Soft Toys" taxon at 20th position
        And I am logged in as an administrator

    @ui
    Scenario: Setting two products to position -1 on the non-last page
        When I am browsing the 1st page of products from "Soft Toys" taxon
        And I set the position of "Old pug" to "-1"
        And I set the position of "Young pug" to "-1"
        And I save my new configuration
        And I go to the 2nd page of products from "Soft Toys" taxon
        Then the one before last product on the list should have name "Old pug"
        And this product should be at position 18
        And the last product on the list should have name "Young pug"
        And this product should be at position 19

    @ui
    Scenario: Setting two products to position -1 on the last page
        When I am browsing the 2nd page of products from "Soft Toys" taxon
        When I set the position of "Pug XL" to "-1"
        And I set the position of "Pug XS" to "-1"
        And I save my new configuration
        Then the one before last product on the list should have name "Pug XL"
        And this product should be at position 18
        And the last product on the list should have name "Pug XS"
        And this product should be at position 19

    @ui
    Scenario: Setting two products to the already occupied position on the other page
        When I am browsing the 1st page of products from "Soft Toys" taxon
        And I set the position of "Old pug" to "15"
        And I set the position of "Young pug" to "15"
        And I save my new configuration
        And I go to the 2nd page of products from "Soft Toys" taxon
        Then the 4th product on this page should be named "Pug Senior"
        And this product should be at position 13
        And the 5th product on this page should be named "Old pug"
        And this product should be at position 14
        And the 6th product on this page should be named "Young pug"
        And this product should be at position 15
        And the 7th product on this page should be named "Pug Master"
        And this product should be at position 16

    @ui
    Scenario: Setting two products to the already occupied position on the same page
        When I am browsing the 2nd page of products from "Soft Toys" taxon
        And I set the position of "Puglet" to "15"
        And I set the position of "Pug XL" to "15"
        And I save my new configuration
        And I go to the 2nd page of products from "Soft Toys" taxon
        Then the 4th product on this page should be named "Pug Senior"
        And this product should be at position 13
        And the 5th product on this page should be named "Puglet"
        And this product should be at position 14
        And the 6th product on this page should be named "Pug XL"
        And this product should be at position 15
        And the 7th product on this page should be named "Pug Master"
        And this product should be at position 16

    @ui
    Scenario: Setting two products to the positions overflowing the max available position on the non-last page
        When I am browsing the 1st page of products from "Soft Toys" taxon
        And I set the position of "Old pug" to "25"
        And I set the position of "Young pug" to "26"
        And I save my new configuration
        And I go to the 2nd page of products from "Soft Toys" taxon
        Then the one before last product on the list should have name "Old pug"
        And this product should be at position 18
        And the last product on the list should have name "Young pug"
        And this product should be at position 19

    @ui
    Scenario: Setting two products to the positions overflowing the max available position on the last page
        When I am browsing the 2nd page of products from "Soft Toys" taxon
        And I set the position of "Puglet" to "25"
        And I set the position of "Pug XL" to "26"
        And I save my new configuration
        And I go to the 2nd page of products from "Soft Toys" taxon
        Then the one before last product on the list should have name "Puglet"
        And this product should be at position 18
        And the last product on the list should have name "Pug XL"
        And this product should be at position 19

    @ui
    Scenario: New product is added as last one
        Given I added a product "Big pug"
        And I assigned this product to "Soft Toys" taxon
        When I am browsing the 3rd page of products from "Soft Toys" taxon
        Then the last product on the list should have name "Big pug"

    @ui
    Scenario: Product with position 0 is set as the first one
        When I am browsing products from "Soft Toys" taxon
        And I set the position of "Young pug" to 0
        And I save my new configuration
        Then the first product on the list should have name "Young pug"

    @ui
    Scenario: Being unable to use a non-numeric string as a product position
        Given I am browsing products from "Soft Toys" taxon
        When I set the position of "Young pug" to "test"
        And I save my new configuration
        Then I should be notified that the position "test" is invalid

#    @ui
#    Scenario: Sort products in descending order
#        When I am browsing products from "Soft Toys" taxon
#        And I start sorting products by position
#        Then I should see 3 products in the list
#        And they should have order like "Small pug", "Young pug" and "Old pug"

#    @ui
#    Scenario: Products are sorted by position in ascending order by default
#        When I am browsing products from "Soft Toys" taxon
#        Then I should see 3 products in the list
#        And they should have order like "Old pug", "Young pug" and "Small pug"
