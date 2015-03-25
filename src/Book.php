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
    $query = $GLOBALS['DB']->query("SELECT author_id FROM books_authors WHERE book_id = {$this->getId()};");
    $author_ids = $query->fetchAll(PDO::FETCH_ASSOC);

    $authors = [];
    foreach ($author_ids as $id) {
      $author_id = $id['author_id'];
      $result = $GLOBALS['DB']->query("SELECT * FROM authors WHERE id = {$author_id};");
      $returned_author = $result->fetchAll(PDO::FETCH_ASSOC);

      $name = $returned_author[0]['name'];
      $id = $returned_author[0]['id'];
      $new_author = new Author($name, $id);
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
