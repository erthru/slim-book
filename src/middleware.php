<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$app->add(function ($request,$response,$next){

    $key = $request->getQueryParam("key");

    if(empty($key)){
        $response->withJson(['status'=>'failed'],502);
    }

    $sql = "SELECT api_key FROM tb_api WHERE api_key='$key'";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    if($stmt->rowCount() > 0){
        return $response = $next($request,$response);
    }else{
        $rsp['status']='Unauthorized';
        return $response->withJson($rsp,401);
    }

});
