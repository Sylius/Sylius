@managing_taxons
Feature: Changing images of an existing taxon
    In order to change images of my categories
    As an Administrator
    I want to be able to changing images of an existing taxon

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Changing a single image of a taxon
        Given the "T-Shirts" taxon has an image "ford.jpg" with "banner" type
        And I want to modify the "T-Shirts" taxon
        When I change the image with the "banner" type to "t-shirts.jpg"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should have an image with "banner" type

    @ui @javascript
    Scenario: Changing the type of image of a taxon
        Given the "T-Shirts" taxon has an image "ford.jpg" with "thumbnail" type
        And the "T-Shirts" taxon also has an image "t-shirts.jpg" with "banner" type
        And I want to modify the "T-Shirts" taxon
        When I change the first image type to "banner"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should still have 2 images
        But it should not have any images with "thumbnail" type
