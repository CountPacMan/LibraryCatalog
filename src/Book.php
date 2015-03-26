<?php
class Book {
  private $title;
  private $id;

  function __construct($title, $id = null) {
    $this->title = $title;
    $this->id = $id;
  }

  // setters
  function setTitle ($title) {
    $this->title = (string) $title;
  }

  function setId($id) {
    $this->id = (int) $id;
  }

  // getters
  function getTitle() {
    return $this->title;
  }

  function getId() {
    return $this->id;
  }
  // dB

  function save() {
    $statement = $GLOBALS['DB']->query("INSERT INTO books (title) VALUES ('{$this->getTitle()}') RETURNING id;");
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $this->setId($result['id']);
  }

  function updateTitle($title) {
    $GLOBALS['DB']->exec("UPDATE books SET title = '{$title}' WHERE id = {$this->getId()}");
    $this->setTitle($title);
  }

  function delete() {
    $GLOBALS['DB']->exec("DELETE FROM books WHERE id = {$this->getId()};");
    $GLOBALS['DB']->exec("DELETE FROM books_authors WHERE book_id = {$this->getId()};");
  }

  function deleteWithAuthor($author_id) {
    $GLOBALS['DB']->exec("DELETE FROM books_authors WHERE book_id = {$this->getId()} AND book_id = {$author_id};");
    $GLOBALS['DB']->exec("DELETE FROM authors WHERE id = {$author_id};");
  }

  function addAuthor($author) {
    $insert = true;
    $query = $GLOBALS['DB']->query("SELECT * FROM books_authors;");
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $result) {
      if ($result['author_id'] == $author->getId() && $result['book_id'] == $this->getId()) {
        $insert = false;
      }
    }
    if ($insert) {
      $GLOBALS['DB']->exec("INSERT INTO books_authors (book_id, author_id) VALUES ({$this->getId()}, {$author->getId()});");
    }
  }

  function getAuthors() {
    $returned_results = $GLOBALS['DB']->query("SELECT authors.* FROM authors JOIN books_authors ON (authors.id = books_authors.author_id) JOIN books ON (books_authors.book_id = books.id) WHERE books.id = {$this->getId()};");
    $authors = [];
    foreach($returned_results as $result) {
      $new_author = new Author($result['name'], $result['id']);
      array_push($authors, $new_author);
    }
    return $authors;
  }

  static function getAll() {
    $returned_books = $GLOBALS['DB']->query("SELECT * FROM books;");
    $books = [];
    foreach ($returned_books as $book) {
      $title = $book['title'];
      $id = $book['id'];
      $new_book = new Book($title, $id);
      array_push($books, $new_book);
    }
    return $books;
  }

  static function deleteAll() {
    $GLOBALS['DB']->exec("DELETE FROM books *;");
    $GLOBALS['DB']->exec("DELETE FROM books_authors *;");
  }

  static function find($search_id) {
    $found_book = null;
    $books = Book::getAll();
    foreach($books as $book) {
      $book_id = $book->getId();
      if($book_id == $search_id) {
        $found_book = $book;
      }
    }
    return $found_book;
  }

  static function search($title) {
    $books = [];
    $returned_books = $GLOBALS['DB']->query("SELECT * FROM books WHERE title = '{$title}';");
    foreach ($returned_books as $book) {
      $new_book = new Book($book['title'], $book['id']);
      array_push($books, $new_book);
    }
    return $books;
  }
}
