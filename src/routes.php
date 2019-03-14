<?php

use Slim\Http\Request;
use Slim\Http\Response;

// default routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});


// books routes

$app->get('/books/{page}', function (Request $request, Response $response, $args){

    $page = $args['page'];

    $limit = 5;
    $start = ($page - 1) * $limit;

    $sql = "SELECT * FROM tb_book ORDER BY book_id DESC LIMIT $start, $limit";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll();

    $rsp = [
        'status' => 'success',
        'data' => $result
    ];

    return $response->withJson($rsp,200);

});

$app->get('/books/search/', function (Request $request, Response $response){

    $keywords = $request->getQueryParam("keywords");

    $limit = 5;

    $sql = "
        SELECT * 
        
        FROM tb_book 
        
        WHERE book_title LIKE '%$keywords%' OR book_author LIKE '%$keywords%' OR book_sinopsis LIKE '%$keywords%' 
        
        ORDER BY book_id DESC LIMIT $limit
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll();

    $rsp = [
        'status' => 'success',
        'data' => $result
    ];

    return $response->withJson($rsp,200);

});

$app->post('/book/', function (Request $request, Response $response){

    $body = $request->getParsedBody();

    $title = $body['book_title'];
    $author = $body['book_author'];
    $sinopsis = $body['book_sinopsis'];

    if(empty($title) || empty($author) || empty($sinopsis)){
        return $response->withJson(['status'=>'failed'],502);
    }else{

        $sql = "INSERT INTO tb_book (book_title,book_author,book_sinopsis) VALUES ('$title','$author','$sinopsis')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rsp = [
            'status'=>'success',
            'data'=>1
        ];

        return $response->withJson($rsp,200);
    }   

});

$app->put('/book/{book_id}',function (Request $request, Response $response, $args){

    $book_id = $args['book_id'];

    $body = $request->getParsedBody();

    $title = $body['book_title'];
    $author = $body['book_author'];
    $sinopsis = $body['book_sinopsis'];

    if(empty($title) || empty($author) || empty($sinopsis)){
        return $response->withJson(['status'=>'failed'],502);
    }else{

        $sql = "UPDATE tb_book SET book_title = '$title', book_author='$author', book_sinopsis='$sinopsis' WHERE book_id = '$book_id'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rsp = [
            'status'=>'success',
            'data'=>1
        ];

        return $response->withJson($rsp,200);

    }

});

$app->delete('/book/{book_id}', function (Request $request, Response $response, $args){

    $book_id = $args['book_id'];

    $sql = "DELETE FROM tb_book WHERE book_id='$book_id'";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    $rsp = [
        'status'=>'success',
        'data'=>1
    ];

    return $response->withJson($rsp,200);

});


