@managing_taxons
Feature: Adding images to an existing taxon
    In order to change images of my categories
    As an Administrator
    I want to be able to add new images to a taxon

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts"
        And I am logged in as an administrator

    @ui @mink:chromedriver @api
    Scenario: Adding a single image to an existing taxon
        When I want to modify the "T-Shirts" taxon
        And I attach the "t-shirts.jpg" image with "banner" type to this taxon
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And this taxon should have an image with "banner" type

    @ui @mink:chromedriver @api
    Scenario: Adding a single image to an existing taxon without specifying the type
        When I want to modify the "T-Shirts" taxon
        And I attach the "t-shirts.jpg" image to this taxon
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And this taxon should have only one image

    @ui @mink:chromedriver @api
    Scenario: Adding multiple images to an existing taxon
        When I want to modify the "T-Shirts" taxon
        And I attach the "t-shirts.jpg" image with "banner" type to this taxon
        And I attach the "t-shirts.jpg" image with "thumbnail" type to this taxon
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And this taxon should have an image with "banner" type
        And it should also have an image with "thumbnail" type

    @ui @javascript @api
    Scenario: Adding multiple images of the same type to an existing taxon
        When I want to modify the "T-Shirts" taxon
        And I attach the "t-shirts.jpg" image with "banner" type to this taxon
        And I attach the "t-shirts.jpg" image with "banner" type to this taxon
        And I save my changes to the images
        Then I should be notified that it has been successfully uploaded
        And this taxon should have 2 images
