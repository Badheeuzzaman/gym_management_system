<?php
$page_title = "POS Sale";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

$items = $pdo->query(
    "SELECT * FROM store_items WHERE stock>0"
)->fetchAll();

$members = $pdo->query(
    "SELECT id,name,member_code FROM members"
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'checkout') {
    $total = 0;
    $cart = json_decode($_POST['cart_data'], true);

    foreach ($cart as $c) {
        $total += $c['price'] * $c['qty'];
    }

    $invoice = generateInvoiceNo();
    $discount = (float) ($_POST['discount'] ?? 0);
    $final = $total - $discount;

    $stmt = $pdo->prepare("INSERT INTO store_sales (invoice_no, member_id, total, discount, final_total, payment_method) VALUES (?,?,?,?,?,?)");
    $stmt->execute([
        $invoice,
        $_POST['member_id'] ?: null,
        $total,
        $discount,
        $final,
        $_POST['payment_method']
    ]);

    $sale_id = $pdo->lastInsertId();

    foreach ($cart as $c) {
        $itemStmt = $pdo->prepare("INSERT INTO store_sale_items (sale_id, item_id, qty, price) VALUES (?,?,?,?)");
        $itemStmt->execute([
            $sale_id,
            $c['id'],
            $c['qty'],
            $c['price']
        ]);

        $updateStock = $pdo->prepare("UPDATE store_items SET stock = stock - ? WHERE id = ?");
        $updateStock->execute([
            $c['qty'],
            $c['id']
        ]);
    }

    $msg = "Sale completed! Invoice: $invoice Total: " . formatCurrency($final);
}
?>
<div class="main-content">
  <div class="topbar">
    <div class="d-flex gap-3 align-items-center">
      <button class="btn btn-light d-lg-none" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>
      <h4>
        <i class="fas fa-cash-register"></i> New Sale (POS)
      </h4>
    </div>
    <div class="topbar-actions">
      <a href="store_sales.php" class="btn btn-light">Sales History</a>
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

<?php if (isset($msg)): ?>
    <div class="alert alert-success">
        <?= $msg ?>
    </div>
<?php endif; ?>
<div class="row g-4">
  <div class="col-lg-7">
    <div class="card-modern">
      <div class="card-header d-flex justify-content-between">
        <span>
          Products (
          <?= count($items) ?>
          )
        </span>
        <input
          class="form-control form-control-sm"
          style="width:200px;"
          placeholder="Search products..."
          id="posSearch"
        >
      </div>
      <div class="card-body">
        <div class="pos-grid" id="posGrid">
          <?php foreach ($items as $it): ?>
            <div
              class="pos-item"
              data-name="<?= strtolower($it['name']) ?>"
              data-item='<?= json_encode($it) ?>'
            >
              <div style="font-weight:600;">
                <?= $it['name'] ?>
              </div>
              <small style="color:#636e72;">
                <?= $it['category'] ?> • Stock: <?= $it['stock'] ?>
              </small>
              <div class="mt-2 d-flex justify-content-between align-items-center">
                <strong style="color:#6c5ce7;">
                  <?= formatCurrency($it['price']) ?>
                </strong>
                <button class="btn btn-sm btn-primary addToCartBtn">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
</div>
</div>
<div class="col-lg-5">
  <div class="card-modern" style="position:sticky;top:90px;">
    <div class="card-header d-flex justify-content-between">
      <span><i class="fas fa-shopping-cart"></i> Cart (<span id="cartCount">0</span>)</span>
      <button class="btn btn-sm btn-light" onclick="clearCart()">Clear</button>
    </div>
    <div class="card-body">
      <form method="post" id="checkoutForm">
        <input type="hidden" name="action" value="checkout">
        <input type="hidden" name="cart_data" id="cartData">
        <div id="cartItems" class="mb-3">
          <div class="text-center p-4" style="color:#636e72;">
            <i class="fas fa-shopping-basket fa-2x mb-2"></i>
            <p>Cart is empty. Click products to add.</p>
          </div>
        </div>
        <div class="border-top pt-3">
          <div class="mb-2">
            <label class="form-label">Member (Optional)</label>
            <select name="member_id" class="form-select">
                <option value="">Walk-in Customer</option>
                <?php foreach ($members as $m): ?>
                    <option value="<?= $m['id'] ?>">
                        <?= $m['member_code'] ?> - <?= $m['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="row g-2 mb-2">
            <div class="col-6">
              <label class="form-label">Discount ($)</label>
              <input name="discount" type="number" step="0.01" value="0" class="form-control" id="discountInput">
            </div>
            <div class="col-6">
              <label class="form-label">Payment Method</label>
              <select name="payment_method" class="form-select">
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="upi">UPI</option>
              </select>
            </div>
          </div>
          <div class="d-flex justify-content-between mb-1">
            <span>Subtotal:</span>
            <strong id="subtotal">$0.00</strong>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Discount:</span>
            <span id="discountDisplay">$0.00</span>
          </div>
          <div class="d-flex justify-content-between mb-3 p-2" style="background:#f0edff;border-radius:8px;">
            <span style="font-weight:700;">Total:</span>
            <strong id="finalTotal" style="color:#6c5ce7;">$0.00</strong>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-3" style="font-weight:700;">Complete Sale 
            <i class="fas fa-arrow-right ms-2"></i>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
</div>
</div>
<script>
let cart = [];

function renderCart() {
    const cartItemsDiv = document.getElementById('cartItems');
    const cartCountSpan = document.getElementById('cartCount');
    const cartDataInput = document.getElementById('cartData');

    cartCountSpan.textContent = cart.length;
    cartDataInput.value = JSON.stringify(cart);

    if (cart.length === 0) {
        cartItemsDiv.innerHTML = '<div class="text-center p-4" style="color:#636e72;"><i class="fas fa-shopping-basket fa-2x mb-2"></i><p>Cart is empty. Click products to add.</p></div>';
    } else {
        cartItemsDiv.innerHTML = cart
            .map((c, i) => `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong>${c.name}</strong><br>
                        <small style="color:#636e72;">
                            ${formatCurrency(c.price)} x ${c.qty} = ${formatCurrency(c.price * c.qty)}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateQty(${i}, -1)">-</button>
                        <span class="badge bg-light text-dark">${c.qty}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="updateQty(${i}, 1)">+</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${i})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>`)
            .join('');
    }

    calcTotals();
}

function addToCart(item) {
    const existing = cart.find((c) => c.id === item.id);

    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({
            ...item,
            qty: 1
        });
    }

    renderCart();
}

function calcTotals() {
    const subtotal = cart.reduce((sum, c) => sum + c.price * c.qty, 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;

    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('discountDisplay').textContent = formatCurrency(discount);
    document.getElementById('finalTotal').textContent = formatCurrency(subtotal - discount);
}

function updateQty(i, d) {
    cart[i].qty += d;

    if (cart[i].qty <= 0) {
        cart.splice(i, 1);
    }

    renderCart();
}

function removeItem(i) {
    cart.splice(i, 1);
    renderCart();
}

function clearCart() {
    cart = [];
    renderCart();
}

document.querySelectorAll('.pos-item').forEach((el) => {
    el.querySelector('.addToCartBtn').addEventListener('click', () => {
        const item = JSON.parse(el.dataset.item);
        addToCart(item);
    });

    el.addEventListener('click', (e) => {
        if (!e.target.closest('button')) {
            const item = JSON.parse(el.dataset.item);
            addToCart(item);
        }
    });
});

document.getElementById('posSearch')?.addEventListener('keyup', function () {
    const q = this.value.toLowerCase();

    document.querySelectorAll('.pos-item').forEach((el) => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
});

document.getElementById('discountInput')?.addEventListener('input', calcTotals);

document.getElementById('checkoutForm')?.addEventListener('submit', function (e) {
    if (cart.length === 0) {
        e.preventDefault();
        alert('Cart is empty!');
    }
});

renderCart();
</script>
<?php require_once '../includes/footer.php'; ?>
