<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$ps = $pdo->query("SELECT r.request_id, r.email_submitted, r.status, r.requested_at,
                          r.handled_at, r.notes,
                          u.first_name, u.last_name, u.role, u.phone,
                          h.first_name AS handler_first, h.last_name AS handler_last
                   FROM password_reset_requests r
                   JOIN users u ON r.user_id = u.user_id
                   LEFT JOIN users h ON r.handled_by_user_id = h.user_id
                   ORDER BY (r.status = 'pending') DESC, r.requested_at DESC
                   LIMIT 100");
$requests = $ps->fetchAll();

$pageTitle = 'Password Reset Requests';
require_once __DIR__ . '/../includes/header.php';

// If a reset was just issued, pull the one-time result for the modal, then clear it.
$resetResult = $_SESSION['reset_result'] ?? null;
unset($_SESSION['reset_result']);
?>

<h1 class="mb-4"><i class="bi bi-key-fill"></i> Password Reset Requests</h1>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><strong>Requests</strong></span>
        <span class="text-muted small"><?= count($requests) ?> total</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($requests)): ?>
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2">No password reset requests.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Requested</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><?= e(formatDateTime($r['requested_at'])) ?></td>
                                <td><?= e($r['first_name'] . ' ' . $r['last_name']) ?></td>
                                <td><span class="badge bg-secondary"><?= e(ucfirst($r['role'])) ?></span></td>
                                <td><?= e($r['email_submitted']) ?></td>
                                <td><?= e($r['phone'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $badge = 'secondary';
                                    if ($r['status'] === 'pending')   $badge = 'warning text-dark';
                                    if ($r['status'] === 'completed') $badge = 'success';
                                    if ($r['status'] === 'rejected')  $badge = 'danger';
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= e(ucfirst($r['status'])) ?></span>
                                    <?php if ($r['handled_at']): ?>
                                        <br><small class="text-muted">
                                            by <?= e($r['handler_first'] ?? 'Unknown') ?>
                                            on <?= e(formatDate($r['handled_at'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($r['status'] === 'pending'): ?>
                                        <form action="<?= BASE_URL ?>/actions/reset-password-action.php" method="POST" class="d-inline">
                                            <input type="hidden" name="request_id" value="<?= (int)$r['request_id'] ?>">
                                            <input type="hidden" name="decision" value="reset">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                    onclick="return confirm('Generate a temporary password for <?= e($r['first_name']) ?>?')">
                                                <i class="bi bi-key"></i> Reset
                                            </button>
                                        </form>
                                        <form action="<?= BASE_URL ?>/actions/reset-password-action.php" method="POST" class="d-inline">
                                            <input type="hidden" name="request_id" value="<?= (int)$r['request_id'] ?>">
                                            <input type="hidden" name="decision" value="reject">
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Reject this reset request?')">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </button>
                                        </form>
                                    <?php elseif (!empty($r['notes'])): ?>
                                        <small class="text-muted"><?= e($r['notes']) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="alert alert-info mt-4">
    <h6 class="alert-heading"><i class="bi bi-info-circle"></i> How this works</h6>
    <p class="mb-0 small">
        Click <strong>Reset</strong> to generate a temporary password. It appears in a popup
        with a <strong>Copy</strong> button - copy it and give it to the user via phone or in
        person, then click <strong>I&rsquo;ve saved this password</strong> to close. The user
        will be required to change it on next login.
    </p>
</div>

<?php if ($resetResult): ?>
<!-- Temporary password modal: blocks until the admin acknowledges -->
<div class="modal fade" id="resetPwModal" tabindex="-1" aria-labelledby="resetPwLabel"
     data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="resetPwLabel">
                    <i class="bi bi-key-fill"></i> Temporary Password Issued
                </h5>
            </div>
            <div class="modal-body">
                <p class="mb-1">
                    For <strong><?= e($resetResult['name']) ?></strong>
                    <span class="text-muted">(<?= e($resetResult['email']) ?>)</span>
                </p>
                <p class="text-muted small mb-3">
                    Copy this password and give it to the user via phone or in person.
                    They will be required to change it on next login.
                </p>

                <div class="input-group">
                    <input type="text" id="tempPwField" class="form-control"
                           style="font-family:monospace; font-size:1.15em; font-weight:bold;"
                           value="<?= e($resetResult['password']) ?>" readonly>
                    <button class="btn btn-outline-primary" type="button" id="copyPwBtn">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </div>
                <div id="copyFeedback" class="form-text text-success" style="visibility:hidden;">
                    Copied to clipboard.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="ackPwBtn" disabled>
                    I&rsquo;ve saved this password
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('resetPwModal');
    var modal = new bootstrap.Modal(modalEl);
    modal.show();

    var pwField  = document.getElementById('tempPwField');
    var copyBtn  = document.getElementById('copyPwBtn');
    var ackBtn   = document.getElementById('ackPwBtn');
    var feedback = document.getElementById('copyFeedback');

    function markCopied() {
        feedback.style.visibility = 'visible';
        copyBtn.innerHTML = '<i class="bi bi-clipboard-check"></i> Copied';
        ackBtn.disabled = false;   // only allow dismiss once they've copied (or tried to)
    }

    copyBtn.addEventListener('click', function () {
        // Prefer the modern clipboard API (works on localhost / https)
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(pwField.value).then(markCopied, fallbackCopy);
        } else {
            fallbackCopy();
        }
    });

    function fallbackCopy() {
        // Fallback: select the field so the admin can copy manually
        pwField.removeAttribute('readonly');
        pwField.select();
        pwField.setSelectionRange(0, 99999);
        try { document.execCommand('copy'); } catch (e) {}
        pwField.setAttribute('readonly', 'readonly');
        markCopied();
    }

    ackBtn.addEventListener('click', function () {
        modal.hide();
    });
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
