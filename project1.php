<?php
// Don't report warnings in the compiler
error_reporting(E_ALL ^ E_WARNING);

// The class for the textbook
class Textbook {
    // Properties
    private string $title;
    private string $publisher;
    private int $edition;
    private int $printing;

    // Creates a new textbook from the request params
    static function CreateFromRequest() : Textbook {
        /** @var Textbook */ $newTextbook = new Textbook();
        
        // Recieved variables from the textbook request
        $newTextbook->title = $_REQUEST["title"];
        $newTextbook->publisher = $_REQUEST["publisher"];
        $newTextbook->edition = intval($_REQUEST["edition"]);
        $newTextbook->printing = intval($_REQUEST["printing"]);

        return $newTextbook;
    }

    // Gets the title of the textbook
    function getTitle() : string {
        return $this->title;
    }

    // Gets the publisher of the textbook
    function getPublisher() : string {
        return $this->publisher;
    }

    // Gets the edition of the textbook
    function getEdition() : int {
        return $this->edition;
    }

    // Gets the printing of the textbook
    function getPrinting() : int {
        return $this->printing;
    }

    // Tests if this textbook is identical to another
    function isIdentical(Textbook $other) : bool {
        return $this->title == $other->title &&
                $this->publisher == $other->publisher &&
                $this->edition == $other->edition &&
                $this->printing == $other->printing;
    }

    // Turns the textbook into link parameters like &title=example
    function toLinkParameters() : string {
        return "&title={$this->title}&publisher={$this->publisher}&edition={$this->edition}&printing={$this->printing}";
    }

    // Turns the textbook into a human readable string
    function toString() : string {
        return "{$this->title}, {$this->publisher}, {$this->edition}, {$this->printing}";
    }
}
// The class for the student
class Student {
    // Properties
    private string $name;
    private array $textbooks;
    private array $courses;

    // Creates a new student from the request params
    static function CreateFromRequest() : Student {
        /** @var Student */ $newStudent = new Student();
        
        // Recieved variables from the student request
        $newStudent->name = $_REQUEST["studentName"];
        $newStudent->textbooks = array();
        $newStudent->courses = array();

        return $newStudent;
    }

    // Gets the name of the student
    function getName() : string {
        return $this->name;
    }

    // Gets the number of textbooks the student has
    function getTextbookCount() : int {
        return count($this->textbooks);
    }

    // Gets the number of courses the student is in
    function getCourseCount() : int {
        return count($this->courses);
    }

    // Gets the textbooks the student has
    function getTextbooks() : array {
        return $this->textbooks;
    }

    // Gets the courses the student is in
    function getCourses() : array {
        return $this->courses;
    }

    // Adds a textbook to the student
    function addTextbook(Textbook $textbook) {
        array_push($this->textbooks, $textbook);
    }

    // Adds a course to the student
    // Secondcall is set to true to prevent an infinite loop
    function addCourse(Course $course, bool $secondCall = false) {
        array_push($this->courses, $course);
        if (!$secondCall) {
            $course->addStudent($this);
        }
    }

    // Determines if the other given student has the same name
    function isIdentical(Student $other) : bool {
        return $this->name == $other->name;
    }

    // Turns the student into link parameters like &name=Ashley
    function toLinkParameters() : string {
        return "&studentName={$this->name}";
    }

    // Turns the student into a human readable string
    function toString() : string {
        return $this->name;
    }
}
// The class for the course
class Course {
    // Properties
    private string $name;
    private array $textbooks;
    private array $students;

    // Creates a new course from the request params
    static function CreateFromRequest() : Course {
        /** @var Course */ $newCourse = new Course();
        
        // Recieved variables from the course request
        $newCourse->name = $_REQUEST["courseName"];
        $newCourse->textbooks = array();
        $newCourse->students = array();

        return $newCourse;
    }

    // Gets the name of the course
    function getName() : string {
        return $this->name;
    }

    // Gets the number of textbooks for the course
    function getTextbookCount() : int {
        return count($this->textbooks);
    }

    // Gets the number of students in the course
    function getStudentCount() : int {
        return count($this->students);
    }

    // Gets the textbooks for the course
    function getTextbooks() : array {
        return $this->textbooks;
    }

    // Gets the students for the course
    function getStudents() : array {
        return $this->students;
    }

    // Adds a textbook to the course
    function addTextbook(Textbook $textbook) {
        array_push($this->textbooks, $textbook);
    }

    // Adds a student to the course
    // Secondcall is set to true to prevent an infinite loop
    function addStudent(Student $student, bool $secondCall = false) {
        array_push($this->students, $student);
        if (!$secondCall) {
            $student->addCourse($this);
        }
    }

    // Checks if the other given course has the exact same name
    function isIdentical(Course $other) : bool {
        return $this->name == $other->name;
    }

    // Turns the course into link parameters like &name=CS453
    function toLinkParameters() : string {
        return "&courseName={$this->name}";
    }

    // Turns the student into a human readable string
    function toString() : string {
        return $this->name;
    }
}

// Reads a serialized object from file
function readFromFile(string $path, $default) {
    if (file_exists($path)) {
        $data = file_get_contents($path);
        $object = unserialize($data);
        if (!empty($object)) {
            return $object;
        }
    }
    return $default;
}

// Serializes an object and writes it to a file
function writeToFile(string $path, $object) {
    $file = fopen($path, "w");
    fwrite($file, serialize($object));
    fclose($file);
}

// The arrays that keep track of everything, and get them from file
/** @var array */ $textbooks = readFromFile("textbooks.txt", array());
/** @var array */ $students = readFromFile("students.txt", array());
/** @var array */ $courses = readFromFile("courses.txt", array());

// Checks if an identical textbook is in the array
function containsIdenticalTextbook(Textbook $textbook, Textbook ...$textbooks) : bool {
    foreach ($textbooks as $other) {
        if ($textbook->isIdentical($other)) {
            return true;
        }
    }
    return false;
}

// Gets an identical textbook from the array
function getIdenticalTextbook(Textbook $textbook, Textbook ...$textbooks) : Textbook|null {
    foreach ($textbooks as $other) {
        if ($textbook->isIdentical($other)) {
            return $other;
        }
    }
    return null;
}

// Checks if another student with the same name exists in the array
function containsIdenticalStudent(Student $student, Student ...$students) : bool {
    foreach ($students as $other) {
        if ($student->isIdentical($other)) {
            return true;
        }
    }
    return false;
}

// Gets an identical student from the array
function getIdenticalStudent(Student $student, Student ...$students) : Student|null {
    foreach ($students as $other) {
        if ($student->isIdentical($other)) {
            return $other;
        }
    }
    return null;
}

// Checks if another course with the same name exists in the array
function containsIdenticalCourse(Course $course, Course ...$courses) : bool {
    foreach ($courses as $other) {
        if ($course->isIdentical($other)) {
            return true;
        }
    }
    return false;
}

// Gets an identical course from the array
function getIdenticalCourse(Course $course, Course ...$courses) : Course|null {
    foreach ($courses as $other) {
        if ($course->isIdentical($other)) {
            return $other;
        }
    }
    return null;
}

// echoes all the given textbooks as items in a table for html
function echoAllTextbooks(Textbook ...$textbooks) {
    foreach ($textbooks as $textbook) {
        echo "<tr>";
        echo "  <td>{$textbook->getTitle()}</td>";
        echo "  <td>{$textbook->getPublisher()}</td>";
        echo "  <td>{$textbook->getEdition()}</td>";
        echo "  <td>{$textbook->getPrinting()}</td>";
        echo "</tr>";
    }
}

// echoes all the given textbooks of a student as items in a dropdown menu for html
function echoAllStudentTextbooks(Student $student, Textbook ...$textbooks) {
    foreach ($textbooks as $option) {
        if (containsIdenticalTextbook($option, ...$student->getTextbooks())) {
            continue;
        }
        // When the value of the dropdown is gotten this will be it, in order for it to be easily appended to a request link
        /** @var string */ $linkParams = $student->toLinkParameters() . $option->toLinkParameters();
        echo "          <option value=\"$linkParams\">{$option->toString()}</option>";
    }
}

// echoes all the given students as items in a table for html
function echoAllStudents(Student ...$students) {
    global $textbooks;

    foreach ($students as $student) {
        echo "<tr>";
        echo "  <td>{$student->getName()}</td>";
        echo "  <td>";
        echo "      <table class=\"bordered\">";
        echo "          <thead>";
        echo "              <tr>";
        echo "                  <td>Title</td>";
        echo "                  <td>Publisher</td>";
        echo "                  <td>Edition</td>";
        echo "                  <td>Printing</td>";
        echo "              </tr>";
        echo "          </thead>";
        echo "          <tbody>";
        echoAllTextbooks(...$student->getTextbooks());
        echo "          </tbody>";
        echo "      </table>";
        echo "      <form action=\"project1.php\">";
        echo "          <select name=\"options\"/>";
        echoAllStudentTextbooks($student, ...$textbooks);
        echo "          </select>";
        echo "          <button type=\"button\" onclick=\"addTextbookToStudent(this.form)\">Add Textbook</button>";
        echo "      </form>";
        echo "  </td>";
        echo "</tr>";
    }
}

// Get the kind of request
/** @var string */ $requestType = $_REQUEST["request"];

// Handle the request
switch ($requestType) {
    // Get all textbooks
    case "getTextbooks": {
        echoAllTextbooks(...$textbooks);
    } break;
    // Add a new textbook
    case "addTextbook": {
        // Recieve textbook from request
        /** @var Textbook */ $newTextbook = Textbook::CreateFromRequest();

        // Check if identical textbook already exists
        if (containsIdenticalTextbook($newTextbook, ...$textbooks)) {
            echo "exists";
            break;
        }

        // Add the textbook to the list of textbooks
        array_push($textbooks, $newTextbook);

        echoAllTextbooks(...$textbooks);
    } break;
    // Get all students
    case "getStudents": {
        echoAllStudents(...$students);
    } break;
    // Add a new student
    case "addStudent": {
        // Revieve student from request
        /** @var Student */ $newStudent = Student::CreateFromRequest();

        // Check if the student with the same name already exists
        if (containsIdenticalStudent($newStudent, ...$students)) {
            echo "exists";
            break;
        }

        // Add the student to the list of students
        array_push($students, $newStudent);
        
        echoAllStudents(...$students);
    } break;
    // Add existing textbook to student
    case "addTextbookToStudent": {
        // Recieve textbook and student from request
        /** @var Student */ $tempStudent = Student::CreateFromRequest();
        /** @var Textbook */ $tempTextbook = Textbook::CreateFromRequest();
        
        // Find student and textbook in list
        /** @var Student */ $student = getIdenticalStudent($tempStudent, ...$students);
        /** @var Textbook */ $textbook = getIdenticalTextbook($tempTextbook, ...$textbooks);

        // Check if either student or textbook were not found
        if (is_null($student)) {
            echo "studentnotfound";
            break;
        }
        if (is_null($textbook)) {
            echo "textbooknotfound";
            break;
        }

        // Add textbook to student
        $student->addTextbook($textbook);

        echoAllStudents(...$students);
    } break;
    default: break;
}

// Write arrays to file
writeToFile("textbooks.txt", $textbooks);
writeToFile("students.txt", $students);
writeToFile("courses.txt", $courses);
?>