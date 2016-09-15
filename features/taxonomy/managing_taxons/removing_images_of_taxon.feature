@managing_taxons
Feature: Removing images of an existing taxon
    In order to remove images of my categories
    As an Administrator
    I want to be able to remove images from an existing taxon

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Removing a single image of a taxon
        Given the "T-Shirts" taxon has an image "t-shirts.jpg" with a code "banner"
        And I want to modify the "T-Shirts" taxon
        When I remove an image with a code "banner"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should not have images

    @ui @javascript
    Scenario: Removing all images of a taxon
        Given the "T-Shirts" taxon has an image "t-shirts.jpg" with a code "banner"
        And the "T-Shirts" taxon has also an image "t-shirts.jpg" with a code "thumbnail"
        And I want to modify the "T-Shirts" taxon
        When I remove an image with a code "banner"
        When I remove also an image with a code "thumbnail"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should not have images

    @ui @javascript
    Scenario: Removing only one image of a taxon
        Given the "T-Shirts" taxon has an image "t-shirts.jpg" with a code "banner"
        And the "T-Shirts" taxon has also an image "t-shirts.jpg" with a code "thumbnail"
        And I want to modify the "T-Shirts" taxon
        When I remove an image with a code "banner"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should have an image with a code "thumbnail"
        But this taxon should not have an image with a code "banner"

    @ui @javascript
    Scenario: Adding multiple images and removing a single image of a taxon
        Given I want to modify the "T-Shirts" taxon
        When I attach the "t-shirts.jpg" image with a code "banner"
        And I attach the "t-shirts.jpg" image with a code "thumbnail"
        And I remove the first image
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should have an image with a code "thumbnail"
        But this taxon should not have an image with a code "banner"
