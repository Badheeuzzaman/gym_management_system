<?php
$page_title = "Sales History";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$sales = $pdo->query(
    "SELECT s.*, m.name as member_name FROM store_sales s LEFT JOIN members m ON s.member_id=m.id ORDER BY s.sale_date DESC"
)->fetchAll();

$total = $pdo->query(
    "SELECT SUM(final_total) FROM store_sales WHERE DATE(sale_date)=CURDATE()"
)->fetchColumn() ?: 0;
?>
<div class="main-content">
    <div class="topbar">
        <div class="d-flex gap-3 align-items-center">
            <button class="btn btn-light d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4>Sales History 
                <small style="color:#636e72;">Today: <?= formatCurrency($total) ?></small>
            </h4>
        </div>
        <div class="topbar-actions">
            <a href="store_pos.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Sale</a>
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
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Discount</th>
                            <th>Final</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $s): ?>
                            <?php
                                $items = $pdo->prepare(
                                    "SELECT si.*, it.name FROM store_sale_items si JOIN store_items it ON si.item_id=it.id WHERE si.sale_id=?"
                                );
                                $items->execute([
                                    $s['id']
                                ]);
                                $its = $items->fetchAll();
                            ?>
                            <tr>
                                <td>
                                    <code><?= $s['invoice_no'] ?></code>
                                </td>
                                <td>
                                    <?= $s['member_name'] ?? 'Walk-in' ?>
                                </td>
                                <td>
                                    <?= formatCurrency($s['total']) ?>
                                </td>
                                <td>
                                    <?= formatCurrency($s['discount']) ?>
                                </td>
                                <td>
                                    <strong><?= formatCurrency($s['final_total']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= $s['payment_method'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('M d H:i', strtotime($s['sale_date'])) ?>
                                </td>
                                <td>
                                    <small>
                                        <?= implode(', ', array_map(fn($x) => $x['name'].' x'.$x['qty'], $its)) ?>
                                    </small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
