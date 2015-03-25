<?php
  require_once __DIR__."/../vendor/autoload.php";
  require_once __DIR__."/../src/Author.php";
  require_once __DIR__."/../src/Book.php";

  $app = new Silex\Application();

  $app['debug'] = true;

  $DB = new PDO('pgsql:host=localhost;dbname=library');

  $app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views'
  ));

  use Symfony\Component\HttpFoundation\Request;
  Request::enableHttpMethodParameterOverride();

  // get

  $app->get("/", function() use ($app) {
    return $app['twig']->render('index.html.twig', array('added' => false, 'books' => Book::getAll()));
  });

  $app->get("/books/{id}", function($id) use ($app) {
    $book = Book::find($id);
    return $app['twig']->render('books.html.twig', array('book' => $book, 'authors' => $book->getAuthors()));
  });

  $app->get("/books/{id}/edit", function($id) use ($app) {
    $book = Book::find($id);
    return $app['twig']->render('books_edit.html.twig', array('book' => $book));
  });

  $app->get("/authors", function() use ($app) {
    $results = Author::getAll();
    $results_books = [];
    foreach ($results as $result) {
      $books = $result->getBooks();
      array_push($results_books, $books);
    }
    return $app['twig']->render('authors.html.twig', array('results_books' => $results_books, 'results' => $results));
  });

  $app->get("/authors/{id}/edit", function($id) use ($app) {
    $author = Author::find($id);
    $books = $author->getBooks();
    $other_books = $author->getOtherBooks();
    return $app['twig']->render('students_edit.html.twig', array('author' => $author, 'books' => $books, 'other_books' => $other_books));
  });

  // post

  $app->post("/books", function() use ($app) {
    $book = new Book($_POST['title']);
    $book->save();
    return $app['twig']->render('index.html.twig', array('added' => false, 'books' => Book::getAll()));
  });

  $app->post("/authors", function() use ($app) {
    $author = new Author($_POST['name']);
    $author->save();
    for ($i = 0; $i < count($_POST['book_id']); $i++) {
      $book = Book::find($_POST['book_id'][$i]);
      $book->addAuthor($author);
    }
    return $app['twig']->render('index.html.twig', array('added' => true, 'books' => Book::getAll()));
  });

  $app->post("/search", function() use ($app) {
    $results = Book::search($_POST['title']);
    return $app['twig']->render('search_results.html.twig', array('results' => $results, 'search_term' => $_POST['title']));
  });

  $app->post("/deleteAuthors", function() use ($app) {
    Author::deleteAll();
    return $app['twig']->render('index.html.twig', array('added' => false));
  });

  $app->post("/deleteBooks", function() use ($app) {
    Book::deleteAll();
    return $app['twig']->render('index.html.twig', array('added' => false));
  });

  // patch

  $app->patch("/books/{id}", function($id) use ($app) {
    $title = $_POST['title'];
    $book = Book::find($id);
    $book->updateTitle($title);
    return $app['twig']->render('books.html.twig', array('book' => $book, 'authors' => $book->getAuthors()));
  });

  $app->patch("/authors/{id}", function($id) use ($app) {
    $name = $_POST['name'];
    $author = Author::find($id);
    $author->updateName($name);
    $author = Author::find($id);
    $books = $author->getBooks();
    $other_books = $author->getOtherBooks();
    return $app['twig']->render('students_edit.html.twig', array('author' => $author, 'books' => $books, 'other_books' => $other_books));
  });

  // delete

  $app->delete("/destroy", function() use ($app) {
    Book::deleteAll();
    Author::deleteAll();
    return $app['twig']->render('index.html.twig', array('added' => false, 'books' => Book::getAll()));
  });

  $app->delete("/books/{id}", function($id) use ($app) {
    $book = Book::find($id);
    $book->delete();
    return $app['twig']->render('index.html.twig', array('added' => false, 'books' => Book::getAll()));
  });

  $app->delete("/author/{id}", function($id) use ($app) {
    $author = Author::find($id);
    $author->delete();
    $book = Book::find($_POST['book_id']);
    return $app['twig']->render('books.html.twig', array('book' => $book, 'authors' => $book->getAuthors()));
  });

  $app->delete("/authors/{id}", function($id) use ($app) {
    $author = Author::find($id);
    $author->deleteWithBook($_POST['book_id']);
    return $app['twig']->render('index.html.twig', array('added' => false, 'books' => Book::getAll()));
  });

  return $app;
?>
