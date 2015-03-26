<?php
  class Author {
    private $name;
    private $id;

    function __construct($name, $id = null)   {
      $this->name = $name;
      $this->id = $id;
    }

    // getters
    function getName()  {
      return $this->name;
    }

    function getId() {
      return $this->id;
    }

    // setters
    function setName($name)  {
      $this->name = (string) $name;
    }

    function setId($id) {
      $this->id = (int) $id;
    }

    // DB

    function save() {
      $statement = $GLOBALS['DB']->query("INSERT INTO authors (name) VALUES ('{$this->getName()}') RETURNING id;");
      $result = $statement->fetch(PDO::FETCH_ASSOC);
      $this->setId($result['id']);
    }

    function updateName($name) {
      $GLOBALS['DB']->exec("UPDATE authors SET name = '{$name}' WHERE id = {$this->getId()}");
      $this->setName($name);
    }

    function addBook($book) {
      $GLOBALS['DB']->exec("INSERT INTO books_authors (book_id, author_id) VALUES ({$book->getId()}, {$this->getId()});");
    }

    function getBooks() {
      $returned_results = $GLOBALS['DB']->query("SELECT books.* FROM books JOIN books_authors ON (books.id = books_authors.book_id) JOIN authors ON (books_authors.author_id = authors.id) WHERE authors.id = {$this->getId()};");
      $books = [];
      foreach($returned_results as $result) {
        $new_book = new Book($result['title'], $result['id']);
        array_push($books, $new_book);
      }
      return $books;
    }

    function getOtherBooks() {
      $query = $GLOBALS['DB']->query("SELECT books.id FROM books LEFT OUTER JOIN books_authors ON books.id = book_id WHERE author_id != {$this->getId()} OR author_id IS null;");
      $book_ids = $query->fetchAll(PDO::FETCH_ASSOC);

      $books = [];
      foreach ($book_ids as $id) {
        $book_id = $id['id'];
        $result = $GLOBALS['DB']->query("SELECT * FROM books WHERE id = {$book_id};");
        $returned_book = $result->fetchAll(PDO::FETCH_ASSOC);
        $name = $returned_book[0]['name'];
        $book_number = $returned_book[0]['book_number'];
        $id = $returned_book[0]['id'];
        $new_book = new Book($name, $book_number, $id);
        array_push($books, $new_book);
      }
      return $books;
    }

    function delete() {
      $GLOBALS['DB']->exec("DELETE FROM authors WHERE id = {$this->getId()};");
      $GLOBALS['DB']->exec("DELETE FROM books_authors WHERE author_id = {$this->getId()};");
    }

    function deleteWithBook($book_id) {
      $GLOBALS['DB']->exec("DELETE FROM books_authors WHERE author_id = {$this->getId()} AND book_id = {$book_id};");
      $GLOBALS['DB']->exec("DELETE FROM books WHERE id = {$book_id};");
    }

    static function find($search_id) {
      $found_author = null;
      $authors = Author::getAll();
      foreach ($authors as $author) {
        $author_id = $author->getId();
        if ($author_id == $search_id) {
          $found_author = $author;
        }
      }
      return $found_author;
    }

    static function getAll() {
      $returned_authors = $GLOBALS['DB']->query("SELECT * FROM authors;");
      $authors = array();
      foreach($returned_authors as $author) {
        $name = $author['name'];
        $id = $author['id'];
        $new_author = new Author($name, $id);
        array_push($authors, $new_author);
      }
      return $authors;
    }

    static function deleteAll() {
      $GLOBALS['DB']->exec("DELETE FROM authors *;");
      $GLOBALS['DB']->exec("DELETE FROM books_authors *;");
    }
  }
?>
