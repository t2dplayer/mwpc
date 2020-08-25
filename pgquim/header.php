<?php
require_once 'student.php';

Settings::get_instance()->add_objects(array(
    new Student('student'),
));
