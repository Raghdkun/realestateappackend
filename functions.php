<?php


define("MB", 1048576);

date_default_timezone_set("Asia/Damascus");

function filterRequest($requestname)
{
    return  htmlspecialchars(strip_tags($_POST[$requestname]));
}

function getAllData($table, $where = null, $values = null, $json = true)
{
    global $con;
    $data = array();
    if ($where == null) {
        $stmt = $con->prepare("SELECT  * FROM $table   ");
    } else {
        $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
    }
    $stmt->execute($values);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count  = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
        return $count;
    } else {
        if ($count > 0) {
            return  array("status" => "success", "data" => $data);
        } else {
            return  array("status" => "failure");
        }
    }
}

function getData($table, $where = null, $values = null, $json = true)
{
    global $con;
    $data = array();
    $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
    $stmt->execute($values);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $count  = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    } else {
        return $count;
    }
}




function insertData($table, $data, $json = true)
{
    global $con;
    foreach ($data as $field => $v)
        $ins[] = ':' . $field;
    $ins = implode(',', $ins);
    $fields = implode(',', array_keys($data));
    $sql = "INSERT INTO $table ($fields) VALUES ($ins)";

    $stmt = $con->prepare($sql);
    foreach ($data as $f => $v) {
        $stmt->bindValue(':' . $f, $v);
    }
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    return $count;
}

function createDynamicLink($dynamicLinkDomain,  $link, $suffix = null) {

    // Set the Firebase Dynamic Links endpoint
    $endpoint = 'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=AAAAJhFuWvQ:APA91bH5M-FzKKaXjqD-caXW69SKrEAzps4cnGa6ZwKkIS2LaWxnp_BbJy3H_5vjgQnyH4bU7OXcfONWf0HkkmUyy2kcan20GM2NFNtQJidRRZtqIb2wYQcyqXxlvUkpXNIxYT9bN5Qz';
  
    // Set the request body
    $body = [
      'dynamicLinkInfo' => [
        'domainUriPrefix' => $dynamicLinkDomain,
        'link' => $link,
        'androidInfo' => [
          'androidPackageName' => "com.powerecommerce.ecommerce",
        ],
        // 'iosInfo' => [
        //   'iosBundleId' => $iosBundleId,
        // ],
      ],
      'suffix' => [
        'option' => $suffix ? $suffix : 'UNGUESSABLE',
      ],
    ];
  
    // Send a POST request to the endpoint
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
  
    // Return the short dynamic link
    $response = json_decode($response, true);
    return $response['shortLink'];
  }


function updateData($table, $data, $where, $json = true)
{
    global $con;
    $cols = array();
    $vals = array();

    foreach ($data as $key => $val) {
        $vals[] = "$val";
        $cols[] = "`$key` =  ? ";
    }
    $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";

    $stmt = $con->prepare($sql);
    $stmt->execute($vals);
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    return $count;
}

function deleteData($table, $where, $json = true)
{
    global $con;
    $stmt = $con->prepare("DELETE FROM $table WHERE $where");
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    return $count;
}

function imageUpload($dir, $imageRequest)
{
    global $msgError;
    if (isset($_FILES[$imageRequest])) {
        $imagename  = rand(1000, 10000) . $_FILES[$imageRequest]['name'];
        $imagetmp   = $_FILES[$imageRequest]['tmp_name'];
        $imagesize  = $_FILES[$imageRequest]['size'];
        $allowExt   = array("jpg", "png", "gif", "mp3", "pdf" , "svg");
        $strToArray = explode(".", $imagename);
        $ext        = end($strToArray);
        $ext        = strtolower($ext);

        if (!empty($imagename) && !in_array($ext, $allowExt)) {
            $msgError = "EXT";
        }
        if ($imagesize > 10 * MB) {
            $msgError = "size";
        }
        if (empty($msgError)) {
            move_uploaded_file($imagetmp,  $dir . "/" . $imagename);
            return $imagename;
        } else {
            return "fail";
        }
    }else {
        return 'empty' ; 
    }
}

function multiImageUpload($dir, $imageRequests, $jsonsize = true, $jsonext = true, $jsonfail = true)
{
    global $msgError;
    $imageNames = array();
    foreach ($imageRequests as $imageRequest) {
        if (isset($_FILES[$imageRequest])) {
            $imagename  = rand(1000, 10000) . $_FILES[$imageRequest]['name'];
            $imagetmp   = $_FILES[$imageRequest]['tmp_name'];
            $imagesize  = $_FILES[$imageRequest]['size'];
            $allowExt   = array("jpg", "png", "gif", "mp3", "pdf" , "svg");
            $strToArray = explode(".", $imagename);
            $ext        = end($strToArray);
            $ext        = strtolower($ext);

            if($jsonext == true){
                if (!empty($imagename) && !in_array($ext, $allowExt)) {
                    $msgError = "EXT";
                    echo     json_encode(array("status" => "EXT"));  
                }
            }
           if($jsonsize == true){
            if ($imagesize > 10 * MB) {
                $msgError = "size";
                echo     json_encode(array("status" => "BigSize"));  
                
            }
           }
            if($jsonfail == true){
                if (empty($msgError)) {
                    move_uploaded_file($imagetmp,  $dir . "/" . $imagename);
                    $imageNames[] = $imagename;
                } else {
                    return 
                    printFailure("none") ;   
    
                }
            }
        } else {
            return 'empty' ; 
             

        }
    }
    return $imageNames;
}


function deleteFile($dir, $imagename)
{
    if (file_exists($dir . "/" . $imagename)) {
        unlink($dir . "/" . $imagename);
    }
}

function checkAuthenticate()
{
    if (isset($_SERVER['PHP_AUTH_USER'])  && isset($_SERVER['PHP_AUTH_PW'])) {
        if ($_SERVER['PHP_AUTH_USER'] != "wael" ||  $_SERVER['PHP_AUTH_PW'] != "wael12345") {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Page Not Found';
            exit;
        }
    } else {
        exit;
    }

    // End 
}


function   printFailure($message = "none")
{
    echo     json_encode(array("status" => "failure", "message" => $message));
}
function   printSuccess($message = "none")
{
    echo     json_encode(array("status" => "success", "message" => $message));
}

function result($count)
{
    if ($count > 0) {
        printSuccess();
    } else {
        printFailure();
    }
}

function sendEmail($to, $title, $body)
{
    $header = "From: contact@blublestore.com " . "\n" . "CC: raghdkun@gmail.com";
    mail($to, $title, $body, $header);
}

function sendGCM($title, $message, $topic, $pageid, $pagename)
{


    $url = 'https://fcm.googleapis.com/fcm/send';

    $fields = array(
        "to" => '/topics/' . $topic,
        'priority' => 'high',
        'content_available' => true,

        'notification' => array(
            "body" =>  $message,
            "title" =>  $title,
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "sound" => "default"

        ),
        'data' => array(
            "pageid" => $pageid,
            "pagename" => $pagename
        )

    );


    $fields = json_encode($fields);
    $headers = array(
        'Authorization: key=' . "AAAAGtxOW4k:APA91bG_cvLw4IP_6errg7ZKzxl6ncl0ycejroJ32oDNOiuSVTujea3LTZEiaaZ7SJI7-W6nU_apsOhrYGPxQ2G0EaRPKx3Lui3KdqMDyLM8qllfoUnRN_ILy-DXZ9LSxoUgpw6N_ttj",
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $result = curl_exec($ch);
    return $result;
    curl_close($ch);
}

function insertNotify($title, $body, $userid, $topic, $pageid, $pagename)
{
    global $con;
    $stmt  = $con->prepare("INSERT INTO `notification`(  `notification_title`, `notification_body`, `notification_usersid`) VALUES (? , ? , ?)");
    $stmt->execute(array($title, $body, $userid));
    sendGCM($title,  $body, $topic, $pageid, $pagename);
    $count = $stmt->rowCount();
    return $count;
}

function generateDeepLink($itemsId) {
    $baseUrl = 'https://blublestore.com';
    $path = '/productdetails/' . $itemsId;
    $deepLink = $baseUrl . $path;
    return $deepLink;
  }