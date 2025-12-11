<?php 
include "header.php";

// Check if user is admin
if (!isset($_COOKIE['admin_auth'])) {
    header("Location: ../login.php");
    exit;
}

$migrations = [
    [
        'file' => 'add_pinned_field.sql',
        'title' => 'Add Pinned Field to Forums & Questions',
        'description' => 'Adds is_pinned column to forums and questions tables for pin posts feature'
    ],
    [
        'file' => 'create_messaging_tables.sql',
        'title' => 'Create Messaging Tables',
        'description' => 'Creates ma_messages and ma_message_threads tables for private messaging system'
    ],
    [
        'file' => 'add_anonymous_field.sql',
        'title' => 'Add Anonymous Posting Field',
        'description' => 'Adds is_anonymous column to forums and questions tables for anonymous posting feature'
    ],
    [
        'file' => 'create_feedback_table.sql',
        'title' => 'Create Feedback Table',
        'description' => 'Creates ma_feedback table for article/question helpfulness ratings (Yes/No buttons)'
    ]
];

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migration'])) {
    $migrationFile = $_POST['migration_file'];
    $sqlFile = "../database/" . basename($migrationFile);
    
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $queries = array_filter(array_map('trim', explode(';', $sql)));
        
        $success = true;
        $errors = [];
        
        foreach ($queries as $query) {
            if (!empty($query)) {
                if (!mysqli_query($con, $query)) {
                    $success = false;
                    $errors[] = mysqli_error($con);
                }
            }
        }
        
        if ($success) {
            $message = "Migration executed successfully: " . basename($migrationFile);
            $messageType = 'success';
        } else {
            $message = "Migration failed: " . implode(", ", $errors);
            $messageType = 'danger';
        }
    } else {
        $message = "Migration file not found: " . $migrationFile;
        $messageType = 'danger';
    }
}
?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Database Migrations</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="index.php"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">System</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Migrations</a></li>
            </ul>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Available Migrations</h4>
                        <p class="card-category">Run these migrations to add new features to the platform</p>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Warning!</strong> Always backup your database before running migrations. These operations modify your database structure.
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="30%">Migration</th>
                                        <th width="50%">Description</th>
                                        <th width="20%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($migrations as $migration): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($migration['title']) ?></strong></td>
                                        <td><?= htmlspecialchars($migration['description']) ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to run this migration? Make sure you have backed up your database.');">
                                                <input type="hidden" name="migration_file" value="<?= htmlspecialchars($migration['file']) ?>">
                                                <button type="submit" name="run_migration" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-play"></i> Run Migration
                                                </button>
                                            </form>
                                            <a href="../database/<?= htmlspecialchars($migration['file']) ?>" class="btn btn-sm btn-info" download>
                                                <i class="fa fa-download"></i> Download SQL
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <h5>Migration Instructions</h5>
                            <ol>
                                <li>Backup your database before running any migration</li>
                                <li>Click "Run Migration" to execute the SQL directly</li>
                                <li>Or download the SQL file and run it manually on your server</li>
                                <li>Check for any errors after migration</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
