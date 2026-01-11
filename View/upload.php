<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Dashboard</a>
            <a class="nav-link active" href="upload.php">Uploads</a>
             <a class="nav-link active" href="userdetails.php">Users</a>
            <a class="nav-link active" href="../index.php">Logout</a>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
                <button class="btn btn-outline-danger" type="submit">Search</button>
            </form>
        </div>
    </nav>
    <div>
        <div class="container mt-5">
            <h2>Uploaded files:</h2>
            <div class="list-group">
            <?php
            $uploadDir = "../assets/uploads/";
            $files = scandir($uploadDir);
            foreach ($files as $file) {
                if ($file !== "." && $file !== "..") {
                    $filePath = $uploadDir . $file;
                    $fileSize = filesize($filePath);
                    $fileSizeKB = round($fileSize / 1024, 2);
                    echo '<div class="list-group-item">';
                    echo '<div class="d-flex justify-content-between align-items-center">';
                    echo '<span>' . htmlspecialchars($file) . ' (' . $fileSizeKB . ' KB)</span>';
                    echo '<div>';
                    echo '<a href="' . htmlspecialchars($filePath) . '" class="btn btn-sm btn-primary" download>Download</a> ';
                    echo '<a href="' . htmlspecialchars($filePath) . '" class="btn btn-sm btn-info" target="_blank">Open</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>