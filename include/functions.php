<?php

function getLink($path){
    if(str_ends_with($path, "/")){
        return SITE_SUBFOLDER . $path;
    }

    return SITE_SUBFOLDER . $path . (SITE_TRAILING_PHP ? ".php" : "");
}

function getRawLink($path){
    return SITE_SUBFOLDER . $path;
}

function getPost($name){
    if(isset($_POST[$name]) && !empty($_POST[$name])){
        return $_POST[$name];
    }

    return null;
}

function getRand($length){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
 
    for($i = 0; $i < $length; $i++){
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
 
    return $randomString;
}

function newPaste($title, $content, $syntax){
    $syntaxes = ["text","bash","brainfuck","c","cpp","csharp","css","dart","go","html","java","javascript","json","kotlin","lua","markdown","php","python","ruby","rust","sql","swift","typescript"];

    if(!$content || empty($content)){
        return ["error" => "Paste content cannot be empty."];
    }

    if(!$title || empty($title)){
        $title = "Untitled Paste";
    }

    if(!$syntax || empty($syntax)){
        $syntax = "text";
    }

    if(!preg_match("/^[a-zA-Z0-9 ]*$/", $title)){
        return ["error" => "Paste title can only contain letters, numbers, and spaces."];
    }

    if(strlen($title) < 3){
        return ["error" => "Paste title must be at least 3 characters long."];
    }

    if(strlen($title) > 100){
        return ["error" => "Paste title cannot be longer than 100 characters."];
    }

    if(strlen($content) < 3){
        return ["error" => "Paste content must be at least 3 characters long."];
    }

    if(!in_array($syntax, $syntaxes)){
        return ["error" => "Invalid syntax."];
    }

    $conn = $GLOBALS["conn"];
    $id = getRand(8);
    $time = time();

    $sql = "INSERT INTO pastes (paste_id, paste_title, paste_content, paste_syntax, paste_time) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $id, $title, $content, $syntax, $time);
    $stmt->execute();

    return ["success" => true, "id" => $id];
}

function getPaste($id){
    if(!$id || empty($id)){
        return;
    }

    $conn = $GLOBALS['conn'];

    $sql = "SELECT * FROM pastes WHERE paste_id = BINARY ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 0){
        return;
    }
    
    return $result->fetch_assoc();
}