<?php
session_start();

if (!isset($_SESSION['admin_logged_in_thinkplusbd']) || $_SESSION['admin_logged_in_thinkplusbd'] !== true) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categories_file_path = __DIR__ . '/categories.json';
    $categories = [];
    if (file_exists($categories_file_path)) {
        $json_data = file_get_contents($categories_file_path);
        $categories = json_decode($json_data, true);
    }

    $new_category = [
        'id' => strtolower(str_replace(' ', '-', $_POST['name'])),
        'name' => $_POST['name'],
        'icon' => $_POST['icon'],
        'subtitle' => $_POST['subtitle'],
        'image' => ''
    ];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_dir = __DIR__ . '/product_images/';
        if (!file_exists($image_dir)) {
            mkdir($image_dir, 0777, true);
        }
        $image_name = basename($_FILES['image']['name']);
        $target_file = $image_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $new_category['image'] = 'product_images/' . $image_name;
        }
    }

    $categories[] = $new_category;

    file_put_contents($categories_file_path, json_encode($categories, JSON_PRETTY_PRINT));

    header("Location: manage_categories.php");
    exit();
}
?>
