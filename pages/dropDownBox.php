<?php
//This is the dropDownBox function made for dropDown boxes in admin panel page and userprofile page
//The variable $valueToBeChecked is the value to be selected.The if-else statement checks if the $valueToBeChecked
//exists in the list of options to be displayed.

function dropDownBox($Array,$valueToBeChecked){
    foreach($Array as $value) {
        if ($valueToBeChecked == $value) {
            echo "<option value=$valueToBeChecked selected>$valueToBeChecked</option>";
        }
        else {
            echo "<option value=$value>$value</option>";
        }
    }
}
