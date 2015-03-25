<?php

  /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

  require_once "src/Book.php";
  require_once "src/Author.php";

  $DB = new PDO('pgsql:host=localhost;dbname=library_test');

  class BookTest extends PHPUnit_Framework_TestCase {

    protected function tearDown() {
      Author::deleteAll();
      Book::deleteAll();
    }

    function test_getTitle() {
      // Arrange
      $title = "Maths";
      $test_book = new Book($title);

      // Act
      $result = $test_book->getTitle();

      // Assert
      $this->assertEquals($title, $result);
    }

    function test_getId() {
      // Arrange
      $title = "Maths";
      $id = 1;
      $test_book = new Book($title, $id);

      // Act
      $result = $test_book->getId();

      // Assert
      $this->assertEquals(1, $result);
    }

    function test_setId() {
      // Assert
      $title = "Maths";
      $test_book = new Book($title);

      // Act
      $test_book->setId(2);
      $result = $test_book->getId();

      // Assert
      $this->assertEquals(2, $result);
    }

    function test_save() {
      // Arrange
      $title = "Maths";
      $test_book = new Book($title);
      $test_book->save();

      // Act
      $result = Book::getAll();

      // Assert
      $this->assertEquals($test_book, $result[0]);
    }

    function test_getAll() {
      // Arrange
      $title = "Maths";
      $title2 = "Sciences";
      $test_book = new Book($title);
      $test_book->save();
      $test_book2 = new Book($title2);
      $test_book2->save();

      // Act
      $result = Book::getAll();

      // Assert
      $this->assertEquals([$test_book, $test_book2], $result);
    }

    function test_deleteAll() {
      // Arrange
      $title = "Maths";
      $title2 = "Sciences";
      $test_book = new Book($title);
      $test_book->save();
      $test_book2 = new Book($title);
      $test_book2->save();

      // Act
      Author::deleteAll();
      Book::deleteAll();
      $result = Book::getAll();

      // Assert
      $this->assertEquals([], $result);
    }

    function testDelete() {
      //Arrange
      $title = "Maths";
      $id = 1;
      $test_book = new Book($title, $id);
      $test_book->save();

      $author_name = "Dennis Lumberg";
      $id2 = 2;
      $test_author = new Author($author_name, $id2);
      $test_author->save();

      //Act
      $test_book->addAuthor($test_author);
      $test_book->delete();

      //Assert
      $this->assertEquals([], $test_author->getBooks());
    }

    function test_find() {
      // Arrange
      $title = "Maths";
      $title2 = "Sciences";
      $test_book = new Book($title);
      $test_book->save();
      $test_book2 = new Book($title2);
      $test_book2->save();

      // Act
      $result = Book::find($test_book->getId());

      // Assert
      $this->assertEquals($test_book, $result);
    }

    function testAddAuthor() {
        //Arrange
        $title = "Maths";
        $id = 1;
        $test_book = new Book($title, $id);
        $test_book->save();

        $author_name = "Dennis Lumberg";
        $id2 = 2;
        $test_author = new Author($author_name, $id2);
        $test_author->save();

        //Act
        $test_book->addAuthor($test_author);

        //Assert
        $this->assertEquals($test_book->getAuthors()[0], $test_author);
    }

    function test_getAuthors() {
      // Arrange
      $title = "Maths";
      $id = 1;
      $test_book = new Book($title, $id);
      $test_book->save();

      $author_name = "Biscuitdoughhandsman";
      $id2 = 2;
      $test_author = new Author($author_name, $id2);
      $test_author->save();

      $author_name2 = "Bob";
      $id3 = 3;
      $test_author2 = new Author($author_name2, $id3);
      $test_author2->save();

      // Act
      $test_book->addAuthor($test_author);
      $test_book->addAuthor($test_author2);

      // Assert
      $this->assertEquals($test_book->getAuthors(), [$test_author, $test_author2]);
    }

    function test_search() {
      // Arrange
      $title = "Maths";
      $test_book = new Book($title);
      $test_book->save();

      $test_book_id = $test_book->getId();
      $author_name = "Biscuitdoughhandsman";
      $test_author = new Author($author_name);
      $test_author->save();

      // Act
      $result = $test_book->search($author_name);

      // Assert
      $this->assertEquals($test_author, $result[0]);
    }

    function test_updateTitle() {
      // Assert
      $title = "Maths";
      $test_book = new Book($title);
      $test_book->save();

      $new_name = "Sciences";

      // Act
      $test_book->updateTitle($new_name);

      // Assert
      $this->assertEquals("Sciences", $test_book->getTitle());
    }

  }
?>
