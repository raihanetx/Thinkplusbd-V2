<?php
session_start();

if (!isset($_SESSION['admin_logged_in_thinkplusbd']) || $_SESSION['admin_logged_in_thinkplusbd'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$categories_file_path = __DIR__ . '/categories.json';
$categories = [];
if (file_exists($categories_file_path)) {
    $json_data = file_get_contents($categories_file_path);
    $categories = json_decode($json_data, true);
}

$edit_category = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    foreach ($categories as $category) {
        if ($category['id'] == $edit_id) {
            $edit_category = $category;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #8F87F1;
            --primary-color-rgb: 143, 135, 241;
            --primary-color-darker: #756dcf;
            --text-color: #343f52;
            --text-muted: #778398;
            --background-color: #f5f7fa;
            --card-bg-color: #ffffff;
            --border-color: #e3e8ee;
            --sidebar-bg: #ffffff;
            --sidebar-text: #525f7f;
            --sidebar-icon-color: #8898aa;
            --sidebar-hover-bg: #f5f7fa;
            --sidebar-hover-text: var(--primary-color);
            --sidebar-active-bg: rgba(var(--primary-color-rgb), 0.1);
            --sidebar-active-text: var(--primary-color);
            --sidebar-active-icon-color: var(--primary-color);
            --font-family-sans-serif: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            --box-shadow: 0 0 30px 0 rgba(82,63,105,0.05);
            --border-radius: 6px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--font-family-sans-serif);
            background-color: var(--background-color);
            color: var(--text-color);
        }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar {
            background-color: var(--sidebar-bg);
            width: 250px;
            padding: 1.75rem 1.25rem;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            box-shadow: var(--box-shadow);
        }
        .admin-main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        .admin-topbar {
            background-color: var(--card-bg-color);
            padding: 0.85rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .admin-topbar h1 { font-size: 1.3rem; color: var(--text-color); margin: 0; font-weight: 600; }
        .admin-page-content { padding: 2rem; }
        .content-card {
            background-color: var(--card-bg-color);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: 1px solid var(--border-color);
            padding: 1.75rem;
            margin-bottom: 2rem;
        }
        .content-card h2.card-title {
            font-size: 1.15rem;
            color: var(--text-color);
            margin-top: 0;
            margin-bottom: 1.25rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
        }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 0.9rem;
        }
        .btn {
            padding: 0.75rem 1.25rem;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: background-color 0.2s ease;
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        .btn-primary:hover { background-color: var(--primary-color-darker); }
        .btn-secondary {
            background-color: #f8f9fa;
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
            vertical-align: middle;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 500;
        }
        .table img {
            max-width: 50px;
            height: auto;
            border-radius: var(--border-radius);
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="logo-admin" style="text-align: center; margin-bottom: 2.5rem;">
                <img src="https://i.postimg.cc/4NtztqPt/IMG-20250603-130207-removebg-preview-1.png" alt="THINK PLUS BD Logo" style="max-height: 45px;">
            </div>
            <nav class="admin-nav">
                <ul>
                    <li><a href="admin_dashboard.php"><i class="fas fa-chart-pie"></i> <span>Dashboard</span></a></li>
                    <li><a href="manage_categories.php" class="active"><i class="fas fa-tags"></i> <span>Manage Categories</span></a></li>
                    <li><a href="product_code_generator.html" target="_blank"><i class="fas fa-plus-circle"></i> <span>Add Product Helper</span></a></li>
                    <li><a href="admin_dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
                </ul>
            </nav>
        </aside>
        <main class="admin-main-content">
            <header class="admin-topbar">
                <h1>Manage Categories</h1>
                <a href="admin_dashboard.php?logout=1" class="logout-btn" style="background-color: transparent; color: var(--primary-color); padding: 0.5rem 1rem; text-decoration: none; border-radius: var(--border-radius); font-weight: 500; border: 1px solid var(--primary-color);"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </header>
            <div class="admin-page-content">
                <div class="content-card">
                    <h2 class="card-title"><?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?></h2>
                    <form action="<?php echo $edit_category ? 'edit_category.php' : 'add_category.php'; ?>" method="POST" enctype="multipart/form-data">
                        <?php if ($edit_category): ?>
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_category['id']); ?>">
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($edit_category['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="icon">Font Awesome Icon</label>
                            <input type="text" id="icon" name="icon" value="<?php echo htmlspecialchars($edit_category['icon'] ?? ''); ?>" placeholder="e.g., fas fa-shopping-cart" required>
                        </div>
                        <div class="form-group">
                            <label for="subtitle">Subtitle</label>
                            <input type="text" id="subtitle" name="subtitle" value="<?php echo htmlspecialchars($edit_category['subtitle'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="image">Category Image</label>
                            <input type="file" id="image" name="image" accept="image/*">
                            <?php if ($edit_category && !empty($edit_category['image'])): ?>
                                <img src="<?php echo htmlspecialchars($edit_category['image']); ?>" alt="Current Image" style="max-width: 100px; margin-top: 10px;">
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary"><?php echo $edit_category ? 'Update Category' : 'Add Category'; ?></button>
                        <?php if ($edit_category): ?>
                            <a href="manage_categories.php" class="btn btn-secondary">Cancel Edit</a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="content-card">
                    <h2 class="card-title">Existing Categories</h2>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Icon</th>
                                    <th>Subtitle</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($category['image'])): ?>
                                                <img src="<?php echo htmlspecialchars($category['image']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                                        <td><i class="<?php echo htmlspecialchars($category['icon']); ?>"></i></td>
                                        <td><?php echo htmlspecialchars($category['subtitle']); ?></td>
                                        <td>
                                            <a href="manage_categories.php?edit_id=<?php echo htmlspecialchars($category['id']); ?>" class="btn btn-secondary">Edit</a>
                                            <a href="delete_category.php?id=<?php echo htmlspecialchars($category['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center;">No categories found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
