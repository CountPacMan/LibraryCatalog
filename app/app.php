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
    return $app['twig']->render('select.html.twig');
  });

  $app->get("/librarian", function() use ($app) {
    return $app['twig']->render('index.html.twig', array('added' => false, 'books' => Book::getAll(), 'author_added' => true, 'authors' => Author::getAll(), 'no_author_fail' => false));
  });

  $app->get("/books/{id}", function($id) use ($app) {
    $book = Book::find($id);
    $books = [];
    array_push($books, $book);
    $authors = [];
    $author = $book->getAuthors();
    array_push($authors, $author);
    return $app['twig']->render('books.html.twig', array('authors' => $authors, 'books' => $books));
  });

  $app->get("/books/{id}/edit", function($id) use ($app) {
    $book = Book::find($id);
    $authors = $book->getAuthors();
    $other_authors = $book->getOtherAuthors();
    return $app['twig']->render('books_edit.html.twig', array('book' => $book, 'authors' => $authors, 'other_authors' => $other_authors));
  });

  $app->get("/books", function() use ($app) {
    $books = Book::getAll();
    $authors = [];
    foreach ($books as $book) {
      $author = $book->getAuthors();
      array_push($authors, $author);
    }
    return $app['twig']->render('books.html.twig', array('authors' => $authors, 'books' => $books));
  });

  $app->get("/authors", function() use ($app) {
    $authors = Author::getAll();
    $books = [];
    foreach ($authors as $author) {
      $book= $author->getBooks();
      array_push($books, $book);
    }
    return $app['twig']->render('authors.html.twig', array('books' => $books, 'authors' => $authors));
  });

  $app->get("/authors/{id}", function($id) use ($app) {
    $author = Author::find($id);
    $authors = [];
    array_push($authors, $author);
    $book = $author->getBooks();
    $books = [];
    array_push($books, $book);
    return $app['twig']->render('authors.html.twig', array('books' => $books, 'authors' => $authors));
  });

  $app->get("/authors/{id}/edit", function($id) use ($app) {
    $author = Author::find($id);
    $books = $author->getBooks();
    $other_books = $author->getOtherBooks();
    return $app['twig']->render('authors_edit.html.twig', array('author' => $author, 'books' => $books, 'other_books' => $other_books));
  });

  // post

  $app->post("/books", function() use ($app) {
    $added = false;
    $no_author_fail = false;
    if (isset($_POST['author_id'])) {
      $book = new Book($_POST['title']);
      $book->save();
      for ($i = 0; $i < count($_POST['author_id']); $i++){
        $author= Author::find($_POST['author_id'][$i]);
        $author->addBook($book);
        $added = true;
      }
    } elseif (!empty($_POST['name'])) {
      $book = new Book($_POST['title']);
      $book->save();
      $author = new Author($_POST['name']);
      $author->save();
      $book->addAuthor($author);
      $added = true;
    } else {
      $no_author_fail = true;
    }

    return $app['twig']->render('index.html.twig', array('added' => $added, 'books' => Book::getAll(), 'author_added' => true, 'authors' => Author::getAll(), 'no_author_fail' => $no_author_fail));
  });

  $app->post("/authors", function() use ($app) {
    $added = false;
    $author_added = true;
    if (isset($_POST['book_id'])) {
      $author = new Author($_POST['name']);
      $author->save();
      for ($i = 0; $i < count($_POST['book_id']); $i++) {
        $book = Book::find($_POST['book_id'][$i]);
        $book->addAuthor($author);
      }
      $added = true;
    } else {
      $author_added = false;
    }
    return $app['twig']->render('index.html.twig', array('added' => $added, 'books' => Book::getAll(), 'author_added' => $author_added, 'authors' => Author::getAll(), 'no_author_fail' => false));
  });

  $app->post("/search/books", function() use ($app) {
    $books = Book::search($_POST['title']);
    $authors = [];
    foreach ($books as $book) {
      $author = $book->getAuthors();
      array_push($authors, $author);
    }
    return $app['twig']->render('books.html.twig', array('authors' => $authors, 'books' => $books));
  });

  $app->post("/search/authors", function() use ($app) {
    $authors = Author::search($_POST['name']);
    $books = [];
    foreach ($authors as $author) {
      $book = $author->getBooks();
      array_push($books, $book);
    }
    return $app['twig']->render('authors.html.twig', array('authors' => $authors, 'books' => $books));
  });

  $app->post("/deleteAuthors", function() use ($app) {
    Author::deleteAll();
    return $app['twig']->render('index.html.twig', array('added' => false, 'author_added' => true, 'no_author_fail' => false));
  });

  $app->post("/deleteBooks", function() use ($app) {
    Book::deleteAll();
    return $app['twig']->render('index.html.twig', array('added' => false, 'author_added' => true, 'no_author_fail' => false));
  });

  // patch

  $app->patch("/books/{id}", function($id) use ($app) {
    $book = Book::find($id);
    if (!empty($_POST['title'])) {
      $book->updateTitle($_POST['title']);
    }
    $authors = [];
    for ($i = 0; $i < count($_POST['author_id']); $i++) {
      $author = Author::find($_POST['author_id'][$i]);
      array_push($authors, $author);
    }
    $book->updateAuthors($authors);
    $authors = $book->getAuthors();
    $other_authors = $book->getOtherAuthors();
    return $app['twig']->render('books_edit.html.twig', array('book' => $book, 'authors' => $authors, 'other_authors' => $other_authors));
  });

  $app->patch("/authors/{id}", function($id) use ($app) {
    $author = Author::find($id);
    if (!empty($_POST['name'])) {
      $author->updateName($_POST['name']);
    }
    $books = [];
    for ($i = 0; $i < count($_POST['book_id']); $i++) {
      $book = Book::find($_POST['book_id'][$i]);
      array_push($books, $book);
    }
    $author->updateBooks($books);
    $books = $author->getBooks();
    $other_books = $author->getOtherBooks();
    return $app['twig']->render('authors_edit.html.twig', array('author' => $author, 'books' => $books, 'other_books' => $other_books));
  });

  // delete

  $app->delete("/destroy", function() use ($app) {
    Book::deleteAll();
    Author::deleteAll();
    return $app['twig']->render('index.html.twig', array('added' => false, 'books' => Book::getAll(), 'author_added' => true, 'authors' => Author::getAll(), 'no_author_fail' => false));
  });

  $app->delete("/books/{id}", function($id) use ($app) {
    $book = Book::find($id);
    $book_authors = $book->getAuthors();
    // if a book the author has written has only one author, delete the book
    foreach ($book_authors as $author) {
      if (count($author->getBooks()) == 1) {
        $book->deleteWithAuthor($author->getId());
      }
    }
    $book->delete();
    return $app['twig']->render('index.html.twig', array('added' => false, 'books' => Book::getAll(), 'author_added' => true, 'authors' => Author::getAll(), 'no_author_fail' => false));
  });

  $app->delete("/authors_pure/{id}", function($id) use ($app) {
    $author = Author::find($id);
    // if a book the author has written has only one author, delete the book
    $author_books = $author->getBooks();
    foreach ($author_books as $book) {
      if (count($book->getAuthors()) == 1) {
        $author->deleteWithBook($book->getId());
      }
    }
    $author->delete();
    $books = [];
    $authors = Author::getAll();
    foreach ($authors as $author) {
      $book = $author->getBooks();
      array_push($books, $book);
    }
    return $app['twig']->render('authors.html.twig', array('books' => $books, 'authors' => $authors));
  });

  return $app;
?>
