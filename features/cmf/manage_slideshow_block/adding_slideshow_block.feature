@managing_slideshow_blocks
Feature: Adding a new slideshow block
    In order to manage slideshow
    As an Administrator
    I want to add slideshow block to my site

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding slideshow block
        Given I want to add a new slideshow block
        When I set its title to "Slideshow for christmas"
        And I set its name to "slide-show-for-christmas"
        And I make it published
        And I make it available from "21.04.2017" to "21.05.2017"
        And I add it
        Then I should be notified that it has been successfully created
