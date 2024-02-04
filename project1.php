<?php
// The class for the textbook
class Textbook {
    // Properties
    private $title;
    private $publisher;
    private $edition;
    private $printing;
    
    // The constructor
    function __construct($title, $publisher, $edition, $printing) {
        $this->title = $title;
        $this->publisher = $publisher;
        $this->edition = $edition;
        $this->printing = $printing;
    }

    // Gets the title of the textbook
    function getTitle() {
        return $this->title;
    }

    // Gets the publisher of the textbook
    function getPublisher() {
        return $this->publisher;
    }

    // Gets the edition of the textbook
    function getEdition() {
        return $this->edition;
    }

    // Gets the printing of the textbook
    function getPrinting() {
        return $this->printing;
    }
}
// The class for the student
class Student {
    // Properties
    private $textbooks;
    private $courses;

    function __construct() {
        $this->textbooks = array();
        $this->courses = array();
    }

    // Gets the number of textbooks the student has
    function getTextbookCount() {
        return count($this->textbooks);
    }

    // Gets the number of courses the student is in
    function getCourseCount() {
        return count($this->courses);
    }

    // Gets the textbooks the student has
    function getTextbooks() {
        return $this->textbooks;
    }

    // Gets the courses the student is in
    function getCourses() {
        return $this->courses;
    }

    // Adds a textbook to the student
    function addTextbook($textbook) {
        array_push($this->textbooks, $textbook);
    }

    // Adds a course to the student
    // Secondcall is set to true to prevent an infinite loop
    function addCourse($course, $secondCall = false) {
        array_push($this->courses, course);
        if (!$secondCall) {
            $course->addStudent($this);
        }
    }
}
// The class for the course
class Course {
    // Properties
    private $textbooks;
    private $students;

    function __construct() {
        $this->textbooks = array();
        $this->students = array();
    }

    // Gets the number of textbooks for the course
    function getTextbookCount() {
        return count($this->textbooks);
    }

    // Gets the number of students in the course
    function getStudentCount() {
        return count($this->students);
    }

    // Gets the textbooks for the course
    function getTextbooks() {
        return $this->textbooks;
    }

    // Gets the students for the course
    function getStudents() {
        return $this->students;
    }

    // Adds a textbook to the course
    function addTextbook($textbook) {
        array_push($this->textbooks, $textbook);
    }

    // Adds a student to the course
    // Secondcall is set to true to prevent an infinite loop
    function addStudent($student, $secondCall = false) {
        array_push($this->students, $course);
        if (!secondCall) {
            $student->addCourse($this);
        }
    }
}

function readFromFile($path, $default) {
    if (file_exists($path)) {
        $data = file_get_contents($path);
        $object = unserialize($data);
        if (!empty($object)) {
            return $object;
        }
    }
    //echo gettype($default);
    return $default;
}

function writeToFile($path, $object) {
    $file = fopen($path, "w");
    fwrite($file, serialize($object));
    fclose($file);
}

// The arrays that keep track of everything, and get them from file
$textbooks = readFromFile("textbooks.txt", array());
$students = readFromFile("students.txt", array());
$courses = readFromFile("courses.txt", array());

// echoes all textbooks as items in a table for html
function echoAllTextbooks() {
    global $textbooks;

    foreach ($textbooks as $textbook) {
        echo "<tr>";
        echo "  <td>{$textbook->getTitle()}</td>";
        echo "  <td>{$textbook->getPublisher()}</td>";
        echo "  <td>{$textbook->getEdition()}</td>";
        echo "  <td>{$textbook->getPrinting()}</td>";
        echo "</tr>";
    }
}

// Get the kind of request
$requestType = $_REQUEST["request"];

// Handle the request
switch ($requestType) {
    // Get all textbooks
    case "getTextbooks": {
        echoAllTextbooks();
    } break;
    // Add a new textbook
    case "addTextbook": {
        // Recieved variables from the textbook request
        $title = $_REQUEST["title"];
        $publisher = $_REQUEST["publisher"];
        $edition = $_REQUEST["edition"];
        $printing = $_REQUEST["printing"];

        array_push($textbooks, new Textbook($title, $publisher, $edition, $printing));

        echoAllTextbooks();
    } break;
    default: break;
}

// Write arrays to file
writeToFile("textbooks.txt", $textbooks);
writeToFile("students.txt", $students);
writeToFile("courses.txt", $courses);
?>