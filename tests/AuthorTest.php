<?php

  /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

  require_once "src/Book.php";
  require_once "src/Author.php";

  $DB = new PDO('pgsql:host=localhost;dbname=library_test');

  class AuthorTest extends PHPUnit_Framework_TestCase {

    protected function tearDown() {
      Author::deleteAll();
      Book::deleteAll();
    }

    function test_save() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $test_author= new Author($name);

      // Act
      $test_author->save();

      // Assert
      $result = Author::getAll();
      $this->assertEquals($test_author, $result[0]);
    }

    function test_getAll() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $name2 = "Bob";
      $test_Author = new Author($name);
      $test_Author->save();
      $test_Author2 = new Author($name2);
      $test_Author2->save();

      // Act
      $result = Author::getAll();

      // Assert
      $this->assertEquals([$test_Author, $test_Author2], $result);
    }

    function test_deleteAll() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $name2 = "Bob";
      $test_Author = new Author($name);
      $test_Author->save();
      $test_Author2 = new Author($name2);
      $test_Author2->save();

      // Act
      Author::deleteAll();

      // Assert
      $result = Author::getAll();
      $this->assertEquals([], $result);
    }

    function testDelete() {
      //Arrange
      $title = "Biscuitdoughhandsman";

      $test_book = new Book($title);
      $test_book->save();

      $name = "Bob";

      $test_author= new Author($name);
      $test_author->save();

      //Act
      $test_author->addBook($test_book);
      $test_author->delete();

      //Assert
      $this->assertEquals([], $test_book->getAuthors());
    }

    function test_getId() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $id = 1;
      $test_Author = new Author($name, $id);

      // Act
      $result = $test_Author->getId();

      // Assert
      $this->assertEquals(1, $result);
    }

    function test_setId() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $test_Author = new Author($name);

      // Act
      $test_Author->setId(2);

      // Assert
      $result = $test_Author->getId();
      $this->assertEquals(2, $result);
    }

    function test_find() {
      // Arrange
      $name = "Biscuitdoughhandsman";
      $name2 = "Bob";
      $test_Author = new Author($name, 1);
      $test_Author->save();
      $test_Author2 = new Author($name2, 1);
      $test_Author2->save();

      // Act
      $result = Author::find($test_Author->getId());

      // Assert
      $this->assertEquals($test_Author, $result);
    }



    function testAddBook() {
      //Arrange
      $title = "Biscuitdoughhandsman";
      $test_book = new Book($title);
      $test_book->save();

      $name = "Bob";
      $test_author= new Author($name);
      $test_author->save();

      //Act
      $test_author->addBook($test_book);

      //Assert
      $this->assertEquals($test_author->getBooks()[0], $test_book);
    }
  }
?>
