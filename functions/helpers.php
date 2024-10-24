<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

