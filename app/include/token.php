<?php
require_once "JWT.php";

// Secret key is something you hardcoded for your server/application
const SECRET_KEY = "S0ftwArePrOjectManAgement1sNotAPr0grAm1ngMOdule!";

/**
 * @param string $username  Login user's username
 * 
 * @return string The token
 */
function generate_token($username) {

    // The username obtained from login form
    return JWT::generate_token($username, SECRET_KEY);
}

/**
 * @param string $token The token to be verified
 * 
 * @return mixed  If verified, return string username.  Else return boolean false.
 */
function verify_token($token) {
    return JWT::verify_token($token, SECRET_KEY);
}