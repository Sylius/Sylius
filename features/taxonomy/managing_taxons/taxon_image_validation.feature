@managing_taxons
Feature: Taxon image validation
    In order to avoid making mistakes when managing a taxon images
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @ui @javascript @todo
    Scenario: Trying to add a new image without specifying its code to an existing taxon
        Given I want to modify the "T-Shirts" taxon
        When I attach the "t-shirts.jpg" image without a code
        And I try to save my changes
        Then I should be notified that an image code is required
        And this taxon should not have images
