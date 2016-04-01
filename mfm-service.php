<?php

/*
 * Author: Richard Damon Ciampa
 * Date: 9/4/2015
 * Class: CST336
 * 
 * 
 * This script provides a RESTful API interface for a web application
 * 
 * $_GET['format'] = [ json | html | xml ]
 * $_GET['method'] = []
 * Output: A formatted HTTP response
 * 
 * 
 */

//SQL Connection      Host:port  User        Password           db
$con = mysqli_connect(".:3306", "ciam1324", "23c160875b24d7f", "sLib_db");
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// --- Step 1: Initialize variables and functions

/**
 * Deliver HTTP Response
 * @param string $format The desired HTTP response content type: [json, html, xml]
 * @param string $api_response The desired HTTP response data
 * @return void
 * */
function deliver_response($format, $api_response) {

    // Define HTTP responses
    $http_response_code = array(
        200 => 'OK',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found'
    );

    // Set HTTP Response
    header('HTTP/1.1 ' . $api_response['status'] . ' ' . $http_response_code[$api_response['status']]);

    // Process different content types
    if (strcasecmp($format, 'json') == 0) {

        // Set HTTP Response Content Type
        header('Content-Type: application/json; charset=utf-8');

        // Format data into a JSON response
        $json_response = json_encode($api_response);

        // Deliver formatted data
        echo $json_response;
    } elseif (strcasecmp($format, 'xml') == 0) {

        // Set HTTP Response Content Type
        header('Content-Type: application/xml; charset=utf-8');

        // Format data into an XML response
        // (This is only good at handling string data, not arrays yet)
        $xml_response = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
                '<response>' . "\n" .
                "\t" . '<code>' . $api_response['code'] . '</code>' . "\n" .
                "\t" . '<data>' . $api_response['data'] . '</data>' . "\n" .
                '</response>';

        // Deliver formatted data
        echo $xml_response;
    } else {

        // Set HTTP Response Content Type (This is only good at handling string data, not arrays)
        header('Content-Type: text/html; charset=utf-8');

        // Deliver formatted data
        echo $api_response['data'];
    }

    // End script process
    exit;
}

// Define whether an HTTPS connection is required
$HTTPS_required = FALSE;

// Define whether user authentication is required
$authentication_required = FALSE;

// Define API response codes and their related HTTP response
$api_response_code = array(
    0 => array('HTTP Response' => 400, 'Message' => 'Unknown Error'),
    1 => array('HTTP Response' => 200, 'Message' => 'Success'),
    2 => array('HTTP Response' => 403, 'Message' => 'HTTPS Required'),
    3 => array('HTTP Response' => 401, 'Message' => 'Authentication Required'),
    4 => array('HTTP Response' => 401, 'Message' => 'Authentication Failed'),
    5 => array('HTTP Response' => 404, 'Message' => 'Invalid Request'),
    6 => array('HTTP Response' => 400, 'Message' => 'Invalid Response Format')
);

// Set default HTTP response of 'ok'
$response['code'] = 0;
$response['status'] = 404;
$response['data'] = NULL;

// --- Step 2: Authorization
// Optionally require connections to be made via HTTPS
if ($HTTPS_required && $_SERVER['HTTPS'] != 'on') {
    $response['code'] = 2;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $api_response_code[$response['code']]['Message'];

    // Return Response to browser. This will exit the script.
    deliver_response($_GET['format'], $response);
}

// Optionally require user authentication
if ($authentication_required) {

    if (empty($_POST['username']) || empty($_POST['password'])) {
        $response['code'] = 3;
        $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
        $response['data'] = $api_response_code[$response['code']]['Message'];

        // Return Response to browser
        deliver_response($_GET['format'], $response);
    }

    // Return an error response if user fails authentication. This is a very simplistic example
    // that should be modified for security in a production environment
    elseif ($_POST['username'] != 'foo' && $_POST['password'] != 'bar') {
        $response['code'] = 4;
        $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
        $response['data'] = $api_response_code[$response['code']]['Message'];

        // Return Response to browser
        deliver_response($_GET['format'], $response);
    }
}

// --- Step 3: Process Request
// Method A: Say Hello to the API
if (strcasecmp($_GET['method'], 'hello') == 0) {
    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = 'Hello World';
}


/*
 * Applicaion methods
 * 
 * The following methods are used for the CST336 class work
 * history applicaiton.
 * 
 */

//1) Method: GetAllBooks()
if (strcasecmp($_GET['method'], 'getAllBooks') == 0) {
    $result = mysqli_query($con, "Call GetAllBooks();");
    $all_recs = array();
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $all_recs[] = $line;
    }

    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $all_recs;
}

//2) Method: CheckIfUserExist()
if (strcasecmp($_GET['method'], 'checkIfUserExist') == 0) {
    $User_Email = $_GET['email'];
    $sql = "Call CheckIfUserExist('" . $User_Email . "')";
    $result = mysqli_query($con, $sql);
    $all_recs = array();
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $all_recs[] = $line;
    }

    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $all_recs;
}

//3) Method: createUserAccount()
if (strcasecmp($_GET['method'], 'createUserAccount') == 0) {
    $user = $_GET['usr'];
    $user_pwd = $_GET['pwd'];
    $sql = "Call CreateUserAccount('" . $user . "', '" . $user_pwd . "')";
    $result = mysqli_query($con, $sql);
    $all_recs = array();
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $all_recs[] = $line;
    }
    
    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $all_recs;
}

//4)Method: placeBookHold()
if (strcasecmp($_GET['method'], 'placeBookHold') == 0) {
    $user = $_GET['usr'];
    $user_pwd = $_GET['pwd'];
    $sql = "Call CreateUserAccount('" . $user . "', '" . $user_pwd . "')";
    $result = mysqli_query($con, $sql);
    $all_recs = array();
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $all_recs[] = $line;
    }
    
    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $all_recs;
}

//5) Method: addTransaction()
if (strcasecmp($_GET['method'], 'addTransaction') == 0) {
    $user = $_GET['usr'];
    $trans = $_GET['transType'];
    $sql = "Call AddTransaction('" . $trans . "', '" . $user . "')";
    $result = mysqli_query($con, $sql);
    $all_recs = array();
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $all_recs[] = $line;
    }
    
    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $all_recs;
}

//6) Method: truncateTransactions()
if (strcasecmp($_GET['method'], 'truncateTransactions') == 0) {
    $sql = "Call TruncateTransactions()";
    $result = mysqli_query($con, $sql);
    $all_recs = array();
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $all_recs[] = $line;
    }
    
    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $all_recs;
}

//7) Method: authenticate()
if (strcasecmp($_GET['method'], 'authenticate') == 0) {
    $user = $_GET['usr'];
    $user_pwd = $_GET['pwd'];
    $sql = "Call Authenticate('" . $user . "', '" . $user_pwd . "')";;
    $result = mysqli_query($con, $sql);
    $all_recs = array();
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $all_recs[] = $line;
    }
    
    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $all_recs;
}

//8) Method: addBookToInventory()
if (strcasecmp($_GET['method'], 'addBookToInventory') == 0) {
    $isbn = $_GET['isbn'];
    $author = $_GET['author'];
    $title = $_GET['title'];
    $fee = $_GET['fee'];
    $sql = "Call addBookToInventory('" . $isbn . "', '" . $author . "', '" . $title . "', '" . $fee ."')";
    $result = mysqli_query($con, $sql);
    $all_recs = array();
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $all_recs[] = $line;
    }
    
    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $all_recs;
}

//9) Method: deleteBookFromInventory()
if (strcasecmp($_GET['method'], 'deleteBookFromInventory') == 0) {
    $isbn = $_GET['isbn'];
    $sql = "Call deleteBookFromInventory('" . $isbn . "')";
    $result = mysqli_query($con, $sql);
    $all_recs = array();
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $all_recs[] = $line;
    }
    
    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $all_recs;
}

//10) Method: updateBookInfo()
if (strcasecmp($_GET['method'], 'updateBookInfo') == 0) {
    $isbn = $_GET['isbn'];
    $author = $_GET['author'];
    $title = $_GET['title'];
    $fee = $_GET['fee'];
    $sql = "Call UpdateBookInfo('" . $isbn . "', '" . $author . "', '" . $title . "', '" . $fee ."')";
    $result = mysqli_query($con, $sql);
    $all_recs = array();
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $all_recs[] = $line;
    }
    
    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $all_recs;
}

//10) Method: getAllTransactions()
if (strcasecmp($_GET['method'], 'getAllTransactions') == 0) {
    $sql = "Call GetAllTransactions()";
    $result = mysqli_query($con, $sql);
    $all_recs = array();
    while ($line = mysqli_fetch_array($result, MYSQL_ASSOC)) {
        $all_recs[] = $line;
    }
    
    $response['code'] = 1;
    $response['status'] = $api_response_code[$response['code']]['HTTP Response'];
    $response['data'] = $all_recs;
}

// --- Step 4: Deliver Response
// Return Response to browser
deliver_response($_GET['format'], $response);
?>

