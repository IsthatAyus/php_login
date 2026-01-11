<?php
require_once __DIR__ . '/../model/auth.php';
session_start();

$user = new User();
if (!$user->isLoggedIn()) {
    header('Location: ../index.php');
    exit();
}

$users = $user->getAllUsers();

$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>User Details</title>
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

    <div class="container mt-5">
        <h2 class="mb-4">Registered Users</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (empty($users)): ?>
            <div class="alert alert-info" role="alert">
                No users found in the database.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Username</th>
                            <th scope="col">Registered At</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $userData): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($userData['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($userData['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($userData['created_at'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $userData['id']; ?>">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $userData['id']; ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $userData['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $userData['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?php echo $userData['id']; ?>">Edit User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="../Controller/update_user_handler.php" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userData['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <div class="mb-3">
                                                    <label for="username<?php echo $userData['id']; ?>" class="form-label">Username</label>
                                                    <input type="text" class="form-control" id="username<?php echo $userData['id']; ?>" name="username" value="<?php echo htmlspecialchars($userData['username'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password<?php echo $userData['id']; ?>" class="form-label">New Password (leave empty to keep current)</label>
                                                    <input type="password" class="form-control" id="password<?php echo $userData['id']; ?>" name="password" placeholder="Enter new password or leave blank">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal<?php echo $userData['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $userData['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteModalLabel<?php echo $userData['id']; ?>">Confirm Delete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="../Controller/delete_user_handler.php" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userData['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <p>Are you sure you want to delete user <strong><?php echo htmlspecialchars($userData['username'], ENT_QUOTES, 'UTF-8'); ?></strong>?</p>
                                                <p class="text-danger">This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">Delete User</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="text-muted">Total users: <?php echo count($users); ?></p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>