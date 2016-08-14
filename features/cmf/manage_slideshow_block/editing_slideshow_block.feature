@managing_slideshow_blocks
Feature: Editing a slideshow
    In order to change slideshow
    As an Administrator
    I want to be able to edit a slideshow

    Background:
        Given I am logged in as an administrator

    @ui @current
    Scenario: Change title of a slideshow
        Given the store has slideshow "Slideshow for Christmas" with name "slideshow-for-christmas"
        And I want to edit this slideshow block
        When I change its title to "Christmas Slideshow"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this slideshow block should have title "Christmas Slideshow"
