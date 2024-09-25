@managing_products
Feature: Reordering product images
    In order to have the most important product image displayed first
    As an Administrator
    I want to be able to reorder product images

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store has a product "Dice Brewing" priced at "$10.00" in "Web-US" channel
        And this product has an image "ford.jpg" with "small" type at position 0
        And this product has an image "ford.jpg" with "medium" type at position 1
        And this product has an image "ford.jpg" with "large" type at position 2
        And I am logged in as an administrator

    @api @ui @mink:chromedriver
    Scenario: Reordering product images
        When I want to modify the images of "Dice Brewing" product
        And I change the "small" image position to 2
        And I change the "medium" image position to 0
        And I change the "large" image position to 1
        Then I save my changes to the images
        And the one before last image on the list should have type "large" with position 1
        And the last image on the list should have type small with position 2
