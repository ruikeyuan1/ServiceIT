<?php
function dropDownBox($Array,$valueToBeChecked){
    foreach($Array as $value) {
        if ($valueToBeChecked== $value) {
            echo "<option value=$valueToBeChecked selected>$valueToBeChecked</option>";
        }
        else {
            echo "<option value=$value>$value</option>";
        }
    }
}
