<?php
$page_title = "Store Items";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = 'SKU-' . strtoupper(substr(md5(uniqid()), 0, 6));
    $stmt = $pdo->prepare(
        "INSERT INTO store_items (name,category,price,stock,sku) VALUES (?,?,?,?,?)"
    );
    $stmt->execute([
        $_POST['name'],
        $_POST['category'],
        $_POST['price'],
        $_POST['stock'],
        $sku
    ]);
}

if (isset($_GET['delete'])) {
    $deleteStmt = $pdo->prepare("DELETE FROM store_items WHERE id=?");
    $deleteStmt->execute([$_GET['delete']]);
    header("Location: store.php");
    exit;
}

$items = $pdo->query(
    "SELECT * FROM store_items ORDER BY id DESC"
)->fetchAll();
?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Store Inventory</h4>
        </div>
        <div class="topbar-actions">
            <button
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addModal"
            >
                <i class="fas fa-plus"></i> Add Item
            </button>
            <div class="dropdown">
                <a
                    href="#"
                    class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    style="color:inherit;"
                >
                    <div
                        class="avatar"
                        style="background:linear-gradient(135deg,#6c5ce7,#a29bfe); color:white;"
                    >
                        <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div
                        class="d-none d-md-block text-start"
                        style="line-height:1.1;"
                    >
                        <div
                            style="font-size:13px; font-weight:600;color:#2d3436;"
                        >
                            <?= $_SESSION['full_name'] ?? 'Admin' ?>
                        </div>
                        <small
                            style="font-size:11px; color:#636e72;"
                        >
                            Administrator
                        </small>
                    </div>
                </a>
                <ul
                    class="dropdown-menu dropdown-menu-end shadow border-0"
                    style="border-radius:12px;min-width:220px;margin-top:8px;"
                >
                    <li class="px-3 py-2">
                        <div class="d-flex gap-2 align-items-center">
                            <div
                                class="avatar"
                                style="background:linear-gradient(135deg,#6c5ce7,#a29bfe);color:white;"
                            >
                                <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                            </div>
                            <div>
                                <strong style="font-size:13px;">
                                    <?= $_SESSION['full_name'] ?? 'Admin' ?>
                                </strong>
                                <br>
                                <small
                                    style="color:#636e72;font-size:11px;"
                                >
                                    <?= $currentUser['email'] ?? 'admin@gym.com' ?>
                                </small>
                            </div>
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item active" href="profile.php">
                            <i class="fas fa-user me-2" style="width:18px;"></i>
                            My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="settings.php">
                            <i class="fas fa-cog me-2" style="width:18px;"></i>
                            Settings
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="my_store_account.php">
                            <i class="fas fa-store me-2" style="width:18px;"></i>
                            My Store Account
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2" style="width:18px;"></i>
                            Sign Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content-wrapper">
        <div class="card-modern">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $it): ?>
                            <tr>
                                <td>
                                    <strong><?= $it['name'] ?></strong>
                                </td>
                                <td>
                                    <code><?= $it['sku'] ?></code>
                                </td>
                                <td>
                                    <?= $it['category'] ?>
                                </td>
                                <td>
                                    <?= formatCurrency($it['price']) ?>
                                </td>
                                <td>
                                    <?= $it['stock'] ?>
                                </td>
                                <td>
                                    <?php if ($it['stock'] < 5): ?>
                                        <span class="badge bg-danger">Low Stock</span>
                                    <?php elseif ($it['stock'] < 10): ?>
                                        <span class="badge bg-warning text-dark">Medium</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">In Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a
                                        href="?delete=<?= $it['id'] ?>"
                                        class="btn btn-sm btn-light text-danger"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5>Add Store Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-2">
                <div class="col-12">
                    <label class="form-label">Name *</label>
                    <input name="name" class="form-control" required placeholder="Whey Protein">
                </div>
                <div class="col-6">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option>Supplement</option>
                        <option>Accessories</option>
                        <option>Beverage</option>
                        <option>Apparel</option>
                        <option>Other</option>
                    </select>
                </div>
                <div class="col-3">
                    <label class="form-label">Price *</label>
                    <input name="price" type="number" step="0.01" class="form-control" required="">
                </div>
                <div class="col-3">
                    <label class="form-label">Stock</label>
                    <input name="stock" type="number" class="form-control" value="10">
                </div>

            </div>

            .modal-footer {
                padding: 1rem;
            }   
        </form>
    </div>
</div>

        
    

