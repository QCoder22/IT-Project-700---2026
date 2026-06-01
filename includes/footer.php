<?php if (isLoggedIn()): ?>
        </div>
    </main>
</div>
<footer class="medicom-footer">
    <div class="container-fluid text-center py-3">
        <small class="text-muted">&copy; <?= date('Y') ?> MedicOM Clinic Management System</small>
    </div>
</footer>
<?php else: ?>
</main>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
