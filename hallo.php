<?
//header("Location: /");
 ignore_user_abort(true);
 ob_start();
 
    $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
    header($serverProtocol . ' 200 OK');
    header('Content-Encoding: none');
    header('Content-Length: ' . ob_get_length());
 	header('Refresh: 1.9;url=/?r=-1');
    header('Connection: close');
 
    ob_end_flush();
    ob_flush();
    flush();

exec("php thumbs.php &");
