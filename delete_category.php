<?php
session_start();

if (!isset($_SESSION['admin_logged_in_thinkplusbd']) || $_SESSION['admin_logged_in_thinkplusbd'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $categories_file_path = __DIR__ . '/categories.json';
    $categories = [];
    if (file_exists($categories_file_path)) {
        $json_data = file_get_contents($categories_file_path);
        $categories = json_decode($json_data, true);
    }

    $delete_id = $_GET['id'];
    $updated_categories = [];

    foreach ($categories as $category) {
        if ($category['id'] != $delete_id) {
            $updated_categories[] = $category;
        }
    }

    file_put_contents($categories_file_path, json_encode($updated_categories, JSON_PRETTY_PRINT));

    header("Location: manage_categories.php");
    exit();
}
?>
