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
        Given the "T-Shirts" taxon has an image "t-shirts.jpg" with "banner" type
        And I want to modify the "T-Shirts" taxon
        When I remove an image with "banner" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should not have any images

    @ui @javascript
    Scenario: Removing all images of a taxon
        Given the "T-Shirts" taxon has an image "t-shirts.jpg" with "banner" type
        And the "T-Shirts" taxon also has an image "t-shirts.jpg" with "thumbnail" type
        And I want to modify the "T-Shirts" taxon
        When I remove an image with "banner" type
        When I also remove an image with "thumbnail" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should not have any images

    @ui @javascript
    Scenario: Removing only one image of a taxon
        Given the "T-Shirts" taxon has an image "t-shirts.jpg" with "banner" type
        And the "T-Shirts" taxon also has an image "t-shirts.jpg" with "thumbnail" type
        And I want to modify the "T-Shirts" taxon
        When I remove an image with "banner" type
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should have an image with "thumbnail" type
        But this taxon should not have any images with "banner" type

    @ui @javascript
    Scenario: Removing only one image of a simple product when all images have same type
        Given the "T-Shirts" taxon has an image "t-shirts.jpg" with "banner" type
        And the "T-Shirts" taxon also has an image "mugs.jpg" with "banner" type
        And I want to modify the "T-Shirts" taxon
        When I remove the first image
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should have only one image

    @ui @javascript
    Scenario: Adding multiple images and removing a single image of a taxon
        Given I want to modify the "T-Shirts" taxon
        When I attach the "t-shirts.jpg" image with "banner" type
        And I attach the "t-shirts.jpg" image with "thumbnail" type
        And I remove the first image
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should have an image with "thumbnail" type
        But this taxon should not have any images with "banner" type
