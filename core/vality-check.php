<?php

function is_valid_string($argument)  {
    if (empty($argument)
        || !is_string($argument)) return false;
    return true;
}