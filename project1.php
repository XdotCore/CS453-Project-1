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
    public static function CreateFromRequest() : Textbook {
        /** @var Textbook */ $newTextbook = new Textbook();
        
        // Recieved variables from the textbook request
        $newTextbook->title = $_REQUEST["title"];
        $newTextbook->publisher = $_REQUEST["publisher"];
        $newTextbook->edition = intval($_REQUEST["edition"]);
        $newTextbook->printing = intval($_REQUEST["printing"]);

        return $newTextbook;
    }

    // Gets the title of the textbook
    public function getTitle() : string {
        return $this->title;
    }

    // Gets the publisher of the textbook
    public function getPublisher() : string {
        return $this->publisher;
    }

    // Gets the edition of the textbook
    public function getEdition() : int {
        return $this->edition;
    }

    // Gets the printing of the textbook
    public function getPrinting() : int {
        return $this->printing;
    }

    // Tests if this textbook is identical to another
    public function isIdentical(Textbook $other) : bool {
        return $this->title == $other->title &&
                $this->publisher == $other->publisher &&
                $this->edition == $other->edition &&
                $this->printing == $other->printing;
    }

    // Turns the textbook into link parameters like &title=example
    public function toLinkParameters() : string {
        return "&title={$this->title}&publisher={$this->publisher}&edition={$this->edition}&printing={$this->printing}";
    }

    // Turns the textbook into a human readable string
    public function toString() : string {
        return "{$this->title}, {$this->publisher}, {$this->edition}, {$this->printing}";
    }
}
// Superclass for student and course that they share
abstract class TextbookHaver {
    protected TextbookList $textbooks;

    public function __construct() {
        $this->textbooks = new TextbookList();
    }

    // Gets the textbooks the student or course has
    public function getTextbooks() : TextbookList {
        return $this->textbooks;
    }

    // Adds a textbook to the student or course
    public function addTextbook(Textbook $textbook) : void {
        $this->textbooks->add($textbook);
    }

    // echoes all the textbook options that a student or course has in a dropdown menu for html
    public function echoAllTextbookOptions(TextbookList $allTextbooks) : void {
        foreach ($allTextbooks as $option) {
            if ($this->textbooks->containsIdentical($option)) {
                continue;
            }
            // When the value of the dropdown is gotten this will be it, in order for it to be easily appended to a request link
            /** @var string */ $linkParams = $this->toLinkParameters() . $option->toLinkParameters();
            echo "          <option value=\"$linkParams\">{$option->toString()}</option>";
        }
    }

    abstract public function toLinkParameters();
}
// The class for the student
class Student extends TextbookHaver {
    // Properties
    private string $name;
    private CourseList $courses;

    // Creates a new student from the request params
    public static function CreateFromRequest() : Student {
        /** @var Student */ $newStudent = new Student();
        
        // Recieved variables from the student request
        $newStudent->name = $_REQUEST["studentName"];
        $newStudent->courses = new CourseList();

        return $newStudent;
    }

    // Gets the name of the student
    public function getName() : string {
        return $this->name;
    }

    // Gets the courses the student is in
    public function getCourses() : CourseList {
        return $this->courses;
    }

    // Adds a course to the student
    // Secondcall is set to true to prevent an infinite loop
    public function addCourse(Course $course, bool $secondCall = false) : void {
        $this->courses->add($course);
        if (!$secondCall) {
            $course->addStudent($this);
        }
    }

    // Determines if the other given student has the same name
    public function isIdentical(Student $other) : bool {
        return $this->name == $other->name;
    }

    // Turns the student into link parameters like &name=Ashley
    public function toLinkParameters() : string {
        return "&studentName={$this->name}";
    }

    // Turns the student into a human readable string
    public function toString() : string {
        return $this->name;
    }
}
// The class for the course
class Course extends TextbookHaver {
    // Properties
    private string $name;
    private StudentList $students;

    // Creates a new course from the request params
    public static function CreateFromRequest() : Course {
        /** @var Course */ $newCourse = new Course();
        
        // Recieved variables from the course request
        $newCourse->name = $_REQUEST["courseName"];
        $newCourse->students = new StudentList();

        return $newCourse;
    }

    // Gets the name of the course
    public function getName() : string {
        return $this->name;
    }

    // Gets the students for the course
    public function getStudents() : StudentList {
        return $this->students;
    }

    // Adds a student to the course
    // Secondcall is set to true to prevent an infinite loop
    public function addStudent(Student $student, bool $secondCall = false) : void {
        $this->students->add($student);
        if (!$secondCall) {
            $student->addCourse($this);
        }
    }

    // Checks if the other given course has the exact same name
    public function isIdentical(Course $other) : bool {
        return $this->name == $other->name;
    }

    // Turns the course into link parameters like &name=CS453
    public function toLinkParameters() : string {
        return "&courseName={$this->name}";
    }

    // Turns the student into a human readable string
    public function toString() : string {
        return $this->name;
    }
}

// Base class for my custom lists
class BaseList implements Iterator {
    protected array $items = array();
    private int $index = 0;

    // gets the number of items in the list
    public function count() : int {
        return count($this->items);
    }

    // gets the current item in a foreach loop
    public function current() : mixed {
        return $this->items[$this->index];
    }

    // gets the current index in a foreach loop
    public function key() : int {
        return $this->index;
    }

    // increments the index in a foreach loop
    public function next() : void {
        $this->index++;
    }

    // resets the index in a foreach loop
    public function rewind() : void {
        $this->index = 0;
    }

    // gets if the index is within bounds in a foreach loop
    public function valid() : bool {
        return $this->index < count($this->items);
    }
}
// List that contains only Textbooks
class TextbookList extends BaseList {
    // gets the current textbook in a foreach loop
    public function current() : Textbook {
        return parent::current();
    }

    // Checks if an identical textbook is in the list
    public function containsIdentical(Textbook $textbook) : bool {
        foreach ($this->items as $other) {
            if ($textbook->isIdentical($other)) {
                return true;
            }
        }
        return false;
    }

    // Gets an identical textbook from the list
    public function getIdentical(Textbook $textbook) : Textbook|null {
        foreach ($this->items as $other) {
            if ($textbook->isIdentical($other)) {
                return $other;
            }
        }
        return null;
    }

    // echoes all the given textbooks as items in a table for html
    public function echoAll() : void {
        foreach ($this->items as $textbook) {
            echo "<tr>";
            echo "  <td>{$textbook->getTitle()}</td>";
            echo "  <td>{$textbook->getPublisher()}</td>";
            echo "  <td>{$textbook->getEdition()}</td>";
            echo "  <td>{$textbook->getPrinting()}</td>";
            echo "</tr>";
        }
    }

    // Adds a new textbook
    public function add(Textbook $textbook) : void {
        array_push($this->items, $textbook);
    }

    // Adds a new textbook from request params and echoes all textbooks
    public function addRequestAndEchoAll() : void {
        // Recieve textbook from request
        /** @var Textbook */ $newTextbook = Textbook::CreateFromRequest();

        // Check if identical textbook already exists
        if ($this->containsIdentical($newTextbook)) {
            echo "exists";
            return;
        }

        // add and echo
        $this->add($newTextbook);
        $this->echoAll();
    }
}
// List that contains only students
class StudentList extends BaseList {
    // gets the current student in a foreach loop
    public function current() : Student {
        return parent::current();
    }

    // Checks if another student with the same name exists in the list
    public function containsIdentical(Student $student) : bool {
        foreach ($this->items as $other) {
            if ($student->isIdentical($other)) {
                return true;
            }
        }
        return false;
    }

    // Gets an identical student from the list
    public function getIdentical(Student $student) : Student|null {
        foreach ($this->items as $other) {
            if ($student->isIdentical($other)) {
                return $other;
            }
        }
        return null;
    }

    // echoes all the given students as items in a table for html
    public function echoAll(TextbookList $allTextbooks) : void {    
        foreach ($this->items as $student) {
            echo "<tr>";
            echo "  <td>{$student->getName()}</td>";
            echo "  <td>";
            // Echo textbooks student has as table
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
            $student->getTextbooks()->echoAll();
            echo "          </tbody>";
            echo "      </table>";
            // Echo textbooks student can get as dropdown and button
            echo "      <form action=\"project1.php\">";
            echo "          <select name=\"options\"/>";
            $student->echoAllTextbookOptions($allTextbooks);
            echo "          </select>";
            echo "          <button type=\"button\" onclick=\"addTextbookToStudent(this.form)\">Add Textbook</button>";
            echo "      </form>";
            echo "  </td>";
            echo "</tr>";
        }
    }

    // Adds a new student
    public function add(Student $student) : void {
        array_push($this->items, $student);
    }

    // Adds a new student from request params and echoes all students
    public function addRequestAndEchoAll(TextbookList $allTextbooks) : void {
        // Revieve student from request
        /** @var Student */ $newStudent = Student::CreateFromRequest();

        // Check if the student with the same name already exists
        if ($this->containsIdentical($newStudent)) {
            echo "exists";
            return;
        }

        // Add student and echo all
        $this->add($newStudent);
        $this->echoAll($allTextbooks);
    }

    // Adds requested existing textbook to requested student and echoes all students
    public function addTextbookRequestAndEchoAll(TextbookList $allTextbooks) : void {
        // Recieve textbook and student from request
        /** @var Student */ $tempStudent = Student::CreateFromRequest();
        /** @var Textbook */ $tempTextbook = Textbook::CreateFromRequest();
        
        // Find student and textbook in list
        /** @var Student */ $student = $this->getIdentical($tempStudent);
        /** @var Textbook */ $textbook = $allTextbooks->getIdentical($tempTextbook);

        // Check if either student or textbook were not found
        if (is_null($student)) {
            echo "studentnotfound";
            return;
        }
        if (is_null($textbook)) {
            echo "textbooknotfound";
            return;
        }

        // Add textbook to student and echo all
        $student->addTextbook($textbook);
        $this->echoAll($allTextbooks);
    }
}
// List that contains only courses
class CourseList extends BaseList {
    // gets the current course in a foreach loop
    public function current() : Course {
        return parent::current();
    }

    // Checks if another course with the same name exists in the list
    public function containsIdentical(Course $course) : bool {
        foreach ($this->items as $other) {
            if ($course->isIdentical($other)) {
                return true;
            }
        }
        return false;
    }

    // Gets an identical course from the list
    public function getIdentical(Course $course) : Course|null {
        foreach ($this->items as $other) {
            if ($course->isIdentical($other)) {
                return $other;
            }
        }
        return null;
    }

    // echoes all the given courses as items in a table for html
    public function echoAll(TextbookList $allTextbooks) : void {
        foreach ($this->items as $course) {
            echo "<tr>";
            echo "  <td>{$course->getName()}</td>";
            echo "  <td>";
            // Echo textbooks course has as table
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
            $course->getTextbooks()->echoAll();
            echo "          </tbody>";
            echo "      </table>";
            // Echo textbooks course can get as dropdown and button, no more after 2
            if ($course->getTextbooks()->count() < 2) {
                echo "  <form action=\"project1.php\">";
                echo "      <select name=\"options\"/>";
                $course->echoAllTextbookOptions($allTextbooks);
                echo "      </select>";
                echo "      <button type=\"button\" onclick=\"addTextbookToCourse(this.form)\">Add Textbook</button>";
                echo "  </form>";
            }
            echo "  </td>";
            echo "</tr>";
        }
    }

    // Adds a new course
    public function add(Course $course) : void {
        array_push($this->items, $course);
    }

    // Adds a new course from request params and echoes all courses
    public function addRequestAndEchoAll(TextbookList $allTextbooks) : void {
        // Recieve course from request
        /** @var Course */ $newCourse = Course::CreateFromRequest();
    
        // Check if the course with the same name already exists
        if ($this->containsIdentical($newCourse)) {
            echo "exists";
            return;
        }

        $this->add($newCourse);
        $this->echoAll($allTextbooks);
    }

    // Adds requested existing textbook to requested course and echoes all courses
    public function addTextbookRequestAndEchoAll(TextbookList $allTextbooks) : void {
        // Recieve textbook and course from request
        /** @var Course */ $tempCourse = Course::CreateFromRequest();
        /** @var Textbook */ $tempTextbook = Textbook::CreateFromRequest();
        
        // Find course and textbook in list
        /** @var Course */ $course = $this->getIdentical($tempCourse);
        /** @var Textbook */ $textbook = $allTextbooks->getIdentical($tempTextbook);

        // Check if either course or textbook were not found
        if (is_null($course)) {
            echo "coursenotfound";
            return;
        }
        if (is_null($textbook)) {
            echo "textbooknotfound";
            return;
        }

        // Add textbook to student and echo all
        $course->addTextbook($textbook);
        $this->echoAll($allTextbooks);
    }
}

// Reads a serialized object from file
function readFromFile(string $path, $default) : mixed {
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
function writeToFile(string $path, $object) : void {
    $file = fopen($path, "w");
    fwrite($file, serialize($object));
    fclose($file);
}

// The arrays that keep track of everything, and get them from file
/** @var TextbookList */ $textbooks = readFromFile("textbooks.txt", new TextbookList());
/** @var StudentList */ $students = readFromFile("students.txt", new StudentList());
/** @var CourseList */ $courses = readFromFile("courses.txt", new CourseList());

// Get the kind of request
/** @var string */ $requestType = $_REQUEST["request"];

// Handle the request
switch ($requestType) {
    // echoes all textbooks
    case "getTextbooks":
        $textbooks->echoAll();
        break;
    // adds a new textbook and echoes all textbooks back
    case "addTextbook":
        $textbooks->addRequestAndEchoAll();
        break;
    // echoes all students
    case "getStudents":
        $students->echoAll($textbooks);
        break;
    // adds a new student and echoes all students back
    case "addStudent": 
        $students->addRequestAndEchoAll($textbooks);
        break;
    // echoes all courses
    case "getCourses":
        $courses->echoAll($textbooks);
        break;
    // adds a new course and echoes all courses back
    case "addCourse":
        $courses->addRequestAndEchoAll($textbooks);
        break;
    // Add existing textbook to student
    case "addTextbookToStudent": 
        $students->addTextbookRequestAndEchoAll($textbooks);
        break;
    // Add existing textbook to course
    case "addTextbookToCourse": 
        $courses->addTextbookRequestAndEchoAll($textbooks);
        break;
    // Add existing student to course
    /*case "addStudentToCourse": {
        // Recieve student and course from request
        /** @var Student  $tempStudent = Course::CreateFromRequest();
        /** @var Course  $tempCourse = Course::CreateFromRequest();

        // Find student and course in list
        /** @var Student  $student = getIdenticalStudent($tempStudent, ...$students);
        /** @var Course  $course = getIdenticalCourse($tempCourse, ...$courses);

        // Check if either student or course were not found
        if (is_null($student)) {
            echo "studentnotfound";
            break;
        }
        if (is_null($course)) {
            echo "coursenotfound";
            break;
        }

        // Add student to course and vice versa
        $course->addStudent($student);
    }*/
    default: break;
}

// Write arrays to file
writeToFile("textbooks.txt", $textbooks);
writeToFile("students.txt", $students);
writeToFile("courses.txt", $courses);
?>