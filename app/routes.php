<?php
// Routes

$app->get('/', App\Action\HomeAction::class)
    ->setName('homepage');

$app->get('/views', function ($request, $response, $args) {
    $tbl = $this->db->prepare('SELECT * FROM news ORDER BY id ASC');
    $tbl->execute();
    $response = $tbl->fetchAll();
    return $this->response->withJson($response);
});

$app->get('/views/[{id}]', function ($request, $response, $args) {
    $tbl = $this->db->prepare('SELECT * FROM news WHERE id=:id');
    $tbl->bindParam("id", $args['id']);
    $tbl->execute();
    $response = $tbl->fetchObject();;
    return $this->response->withJson($response);
});

$app->post('/store', function ($request, $response) {
    $input = $request->getParsedBody();
    $tbl = $this->db->prepare('INSERT INTO news(title,content) VALUE (:title,:content)');
    $tbl->bindParam('title', $input['title']);
    $tbl->bindParam('content', $input['content']);
    $tbl->execute();

    return $this->response->withJson($input);
});

$app->put('/update/[{id}]', function ($request, $response, $args) {
    $input = $request->getParsedBody();
    $tbl = $this->db->prepare('UPDATE news SET title=(:title),content=(:content) WHERE id=(:id)');
    $tbl->bindParam('title', $input['title']);
    $tbl->bindParam('content', $input['content']);
    $tbl->bindParam('id', $args['id']);
    $tbl->execute();
    $input['id'] = $args['id'];
    return $this->response->withJson($input);
});


$app->delete('/delete/[{id}]', function ($request, $response, $args) {
    $tbl = $this->db->prepare('SELECT * FROM news WHERE id=:id');
    $tbl->bindParam("id", $args['id']);
    $tbl->execute();
    $exec = $tbl->fetchObject();
    if ($exec == TRUE) {
        $tbl = $this->db->prepare('DELETE FROM news WHERE id=(:id)');
        $tbl->bindParam('id', $args['id']);
        $tbl->execute();
        $response = array('status' => 'Sukses Delete');
        return $this->response->withJson($response);
    } else {
        $response = array('status' => 'Gagal Delete');
        return $this->response->withJson($response);
    }
});
