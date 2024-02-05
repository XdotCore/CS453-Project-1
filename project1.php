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
    public function addCourse(Course $course) : void {
        $this->courses->add($course);
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
    public function addStudent(Student $student) : void {
        $this->students->add($student);
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

    // echoes all the student options that a course has in a dropdown menu for html
    public function echoAllStudentOptions(StudentList $allStudents) : void {
        foreach ($allStudents as $option) {
            if ($this->students->containsIdentical($option)) {
                continue;
            }
            // When the value of the dropdown is gotten this will be it, in order for it to be easily appended to a request link
            /** @var string */ $linkParams = $this->toLinkParameters() . $option->toLinkParameters();
            echo "          <option value=\"$linkParams\">{$option->toString()}</option>";
        }
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
        foreach ($this as $other) {
            if ($textbook->isIdentical($other)) {
                return true;
            }
        }
        return false;
    }

    // Gets an identical textbook from the list
    public function getIdentical(Textbook $textbook) : Textbook|null {
        foreach ($this as $other) {
            if ($textbook->isIdentical($other)) {
                return $other;
            }
        }
        return null;
    }

    // Gets the indeces of an identical textbook from the list
    public function getIdenticalIndeces(Textbook $textbook) : array {
        $indeces = array();
        for ($i = 0; $i < $this->count(); $i++) {
            if ($textbook->isIdentical($this->get($i))) {
                array_push($indeces, $i);
            }
        }
        return $indeces;
    }

    // Gets a textbook with the same name
    public function getSameName(Textbook $textbook) : Textbook|null {
        foreach($this as $other) {
            if ($textbook->getTitle() == $other->getTitle()) {
                return $other;
            }
        }
        return null;
    }

    // echoes all the given textbooks as items in a table for html
    public function echoAll(bool $showDifference = false, TextbookList $courseTextbooks = null) : void {
        foreach ($this as $textbook) {
            echo "<tr>";
            if (!$showDifference) {
                // Just print out the textbook without color if not showing dif
                    echo "<td>{$textbook->getTitle()}</td>";
                    echo "<td>{$textbook->getPublisher()}</td>";
                    echo "<td>{$textbook->getEdition()}</td>";
                    echo "<td>{$textbook->getPrinting()}</td>";
            } else {
                // Print out with color of what's wrong or right
                $titleColor = "";
                $publisherColor = "";
                $editionColor = "";
                $printingColor = "";
                
                // If the textbook is identical to one of the requirements then make all green
                if (!is_null($courseTextbooks->getIdentical($textbook))) {
                    $titleColor = "right";
                    $publisherColor = "right";
                    $editionColor = "right";
                    $printingColor = "right";
                } else {
                    $sameTitle = $courseTextbooks->getSameName($textbook);
                    // If the title doesn't match any on of the requirements them make all red
                    if (is_null($sameTitle)) {
                        $titleColor = "wrong";
                        $publisherColor = "wrong";
                        $editionColor = "wrong";
                        $printingColor = "wrong";
                    } else  {
                        $titleColor = "right";
                        // since title is the same, check the rest
                        $publisherColor = $textbook->getPublisher() == $sameTitle->getPublisher() ? "right" : "wrong";
                        $editionColor = $textbook->getEdition() == $sameTitle->getEdition() ? "right" : "wrong";
                        $printingColor = $textbook->getPrinting() == $sameTitle->getPrinting() ? "right" : "wrong";
                    }
                }

                // print with colors now
                echo "<td class=\"$titleColor\">{$textbook->getTitle()}</td>";
                echo "<td class=\"$publisherColor\">{$textbook->getPublisher()}</td>";
                echo "<td class=\"$editionColor\">{$textbook->getEdition()}</td>";
                echo "<td class=\"$printingColor\">{$textbook->getPrinting()}</td>";
            }
            echo "</tr>";
        }
    }

    // Adds a new textbook
    public function add(Textbook $textbook) : void {
        array_push($this->items, $textbook);
    }

    // replaces the textbook at the index
    public function set(int $index, Textbook $textbook) : void {
        $this->items[$index] = $textbook;
    }

    // gets the textbook at the index
    public function get(int $index) : Textbook {
        return $this->items[$index];
    }

    // Replaces identical textbooks in students and courses with ones from this list
    public function replaceIdenticalIn(StudentList $allStudents, CourseList $allCourses) {
        foreach ($this as $textbook) {
            // Replace all in students
            foreach ($allStudents as $student) {
                foreach ($student->getTextbooks()->getIdenticalIndeces($textbook) as $i) {
                    $student->getTextbooks()->set($i, $textbook);
                }
            }
            // Replace all in courses
            foreach ($allCourses as $course) {
                foreach ($course->getTextbooks()->getIdenticalIndeces($textbook) as $i) {
                    $course->getTextbooks()->set($i, $textbook);
                }
            }
        }
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
        foreach ($this as $other) {
            if ($student->isIdentical($other)) {
                return true;
            }
        }
        return false;
    }

    // Gets an identical student from the list
    public function getIdentical(Student $student) : Student|null {
        foreach ($this as $other) {
            if ($student->isIdentical($other)) {
                return $other;
            }
        }
        return null;
    }

    // Gets the indeces of an identical students from the list
    public function getIdenticalIndeces(Student $student) : array {
        $indeces = array();
        for ($i = 0; $i < $this->count(); $i++) {
            if ($student->isIdentical($this->get($i))) {
                array_push($indeces, $i);
            }
        }
        return $indeces;
    }

    // echoes all the given students as items in a table for html
    public function echoAll(TextbookList $allTextbooks, bool $showDifference = false, TextbookList $courseTextbooks = null) : void {    
        foreach ($this as $student) {
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
            $student->getTextbooks()->echoAll($showDifference, $courseTextbooks);
            echo "          </tbody>";
            echo "      </table>";
            // Echo textbooks student can get as dropdown and button
            // don't show when in the course difference shower
            if (!$showDifference) {
                echo "  <form action=\"project1.php\">";
                echo "      <select name=\"options\"/>";
                $student->echoAllTextbookOptions($allTextbooks);
                echo "      </select>";
                echo "      <button type=\"button\" onclick=\"addTextbookToStudent(this.form)\">Add Textbook</button>";
                echo "  </form>";
            }
            echo "  </td>";
            echo "</tr>";
        }
    }

    // Adds a new student
    public function add(Student $student) : void {
        array_push($this->items, $student);
    }

    // replaces the student at the index
    public function set(int $index, Student $student) : void {
        $this->items[$index] = $student;
    }

    // gets the student at the index
    public function get(int $index) : Student {
        return $this->items[$index];
    }

    // Replaces identical students in courses with ones from this list
    public function replaceIdenticalIn(CourseList $allCourses) {
        foreach ($this as $student) {
            // Replace all in courses
            foreach ($allCourses as $course) {
                foreach ($course->getStudents()->getIdenticalIndeces($student) as $i) {
                    $course->getStudents()->set($i, $student);
                }
            }
        }
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
        foreach ($this as $other) {
            if ($course->isIdentical($other)) {
                return true;
            }
        }
        return false;
    }

    // Gets an identical course from the list
    public function getIdentical(Course $course) : Course|null {
        foreach ($this as $other) {
            if ($course->isIdentical($other)) {
                return $other;
            }
        }
        return null;
    }

    // Gets the index of an identical course from the list
    public function getIdenticalIndeces(Course $course) : array {
        $indeces = array();
        for ($i = 0; $i < $this->count(); $i++) {
            if ($course->isIdentical($this->get($i))) {
                array_push($indeces, $i);
            }
        }
        return $indeces;
    }

    // echoes all the given courses as items in a table for html
    public function echoAll(StudentList $allStudents, TextbookList $allTextbooks) : void {
        foreach ($this as $course) {
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
                echo "      <select name=\"options\">";
                $course->echoAllTextbookOptions($allTextbooks);
                echo "      </select>";
                echo "      <button type=\"button\" onclick=\"addTextbookToCourse(this.form)\">Add Textbook</button>";
                echo "  </form>";
            }
            // Echo students course has as table
            echo "  </td>";
            echo "  <td>";
            echo "      <table class=\"bordered\">";
            echo "          <thead>";
            echo "              <tr>";
            echo "                  <td>Name</td>";
            echo "                  <td>Textbooks</td>";
            echo "              </tr>";
            echo "          </thead>";
            echo "          <tbody>";
            $course->getStudents()->echoAll($allTextbooks, true, $course->getTextbooks());
            echo "          </tbody>";
            echo "      </table>";
            echo "      <form action=\"project1.php\">";
            echo "          <select name=\"options\">";
            $course->echoAllStudentOptions($allStudents);
            echo "          </select>";
            echo "          <button type=\"button\" onclick=\"addStudentToCourse(this.form)\">Add Student</button>";
            echo "      </form>";
            echo "  </td>";
            echo "</tr>";
        }
    }

    // Adds a new course
    public function add(Course $course) : void {
        array_push($this->items, $course);
    }

    // replaces the course at the index
    public function set(int $index, Course $course) : void {
        $this->items[$index] = $course;
    }

    // gets the course at the index
    public function get(int $index) : Course {
        return $this->items[$index];
    }

    // Replaces identical courses in students with ones from this list
    public function replaceIdenticalIn(StudentList $allStudents) {
        foreach ($this as $course) {
            // Replace all in students
            foreach ($allStudents as $student) {
                foreach ($student->getCourses()->getIdenticalIndeces($course) as $i) {
                    $student->getCourses()->set($i, $course);
                }
            }
        }
    }

    // Adds a new course from request params and echoes all courses
    public function addRequestAndEchoAll(StudentList $allStudents, TextbookList $allTextbooks) : void {
        // Recieve course from request
        /** @var Course */ $newCourse = Course::CreateFromRequest();
    
        // Check if the course with the same name already exists
        if ($this->containsIdentical($newCourse)) {
            echo "exists";
            return;
        }

        $this->add($newCourse);
        $this->echoAll($allStudents, $allTextbooks);
    }

    // Adds requested existing textbook to requested course and echoes all courses
    public function addTextbookRequestAndEchoAll(StudentList $allStudents, TextbookList $allTextbooks) : void {
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
        $this->echoAll($allStudents, $allTextbooks);
    }

    // Adds requested existing student to requested course and echoes all courses
    public function addStudentRequestedAndEchoAll(StudentList $allStudents, TextbookList $allTextbooks) : void {
        // Recieve student and course from request
        /** @var Student */ $tempStudent = Student::CreateFromRequest();
        /** @var Course */ $tempCourse = Course::CreateFromRequest();

        // Find student and course in list
        /** @var Student */ $student = $allStudents->getIdentical($tempStudent);
        /** @var Course */ $course = $this->getIdentical($tempCourse);

        // Check if either student or course were not found
        if (is_null($student)) {
            echo "studentnotfound";
            return;
        }
        if (is_null($course)) {
            echo "coursenotfound";
            return;
        }

        // Add student to course and vice versa, then 
        $course->addStudent($student);
        $student->addCourse($course);
        $this->echoAll($allStudents, $allTextbooks);
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

// Replace identical elements with the ones from a main list such that modifying an item will affect the same item in another item's list
$textbooks->replaceIdenticalIn($students, $courses);
$students->replaceIdenticalIn($courses);
$courses->replaceIdenticalIn($students);

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
        $courses->echoAll($students, $textbooks);
        break;
    // adds a new course and echoes all courses back
    case "addCourse":
        $courses->addRequestAndEchoAll($students, $textbooks);
        break;
    // Add existing textbook to student
    case "addTextbookToStudent": 
        $students->addTextbookRequestAndEchoAll($textbooks);
        break;
    // Add existing textbook to course
    case "addTextbookToCourse": 
        $courses->addTextbookRequestAndEchoAll($students, $textbooks);
        break;
    // Add existing student to course
    case "addStudentToCourse":
        $courses->addStudentRequestedAndEchoAll($students, $textbooks);
        break;
    default: break;
}

// Write arrays to file
writeToFile("textbooks.txt", $textbooks);
writeToFile("students.txt", $students);
writeToFile("courses.txt", $courses);
?>