# features/frontpage.feature
Feature: First Page
	In order to find a spare part for my car
	As a web user
	I need to select make and model, and search for the spare part

	Scenario: Selecting a Model based on a make selected
		Given I am on "http://bildelspriser.dev"
		When I select "Volvo" from "make_select"
		#Then select "model_select" contains "480"
		Then I should see "480" in the "model_select" element