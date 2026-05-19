<?php
require_once "../config/db.php";
?>

<h2>Issue Prescription</h2>

<form method="POST" action="../actions/issue-prescription-action.php">

    <!-- Patient + Appointment -->
    <input name="patient_id" placeholder="Patient ID" required>
    <input name="appointment_id" placeholder="Appointment ID" required>

    <br><br>

    <!-- Medication Container -->
    <div id="meds">

        <div class="med-row">

            Medication:

            <select name="medication_id[]" onchange="checkStock(this)" required>

                <option value="">Select Medication</option>

                <?php
                $stmt = $pdo->query("SELECT * FROM inventory");

                while ($row = $stmt->fetch()) {
                ?>

                    <option value="<?= $row['id'] ?>">

                        <?= $row['medication_name'] ?>
                        (Stock: <?= $row['quantity'] ?>)

                    </option>

                <?php } ?>

            </select>

            Dosage:
            <input name="dosage[]" required>

            Qty:
            <input name="quantity[]" type="number" required>

            <span class="stock-status"></span>

        </div>

    </div>

    <br>

    <button type="button" onclick="addRow()">+ Add</button>

    <button type="submit">Submit</button>

</form>

<script>

function addRow() {

    let html = `
    <div class="med-row" style="margin-top:10px;">

        Medication:

        <select name="medication_id[]" onchange="checkStock(this)" required>

            <option value="">Select Medication</option>

            <?php
            $stmt = $pdo->query("SELECT * FROM inventory");

            while ($row = $stmt->fetch()) {
            ?>

                <option value="<?= $row['id'] ?>">

                    <?= $row['medication_name'] ?>
                    (Stock: <?= $row['quantity'] ?>)

                </option>

            <?php } ?>

        </select>

        Dosage:
        <input name="dosage[]" required>

        Qty:
        <input name="quantity[]" type="number" required>

        <span class="stock-status"></span>

    </div>
    `;

    document.getElementById("meds").insertAdjacentHTML("beforeend", html);
}

function checkStock(select) {

    let id = select.value;

    fetch("../actions/check-stock.php?id=" + id)

    .then(res => res.json())

    .then(data => {

        let span = select.parentElement.querySelector(".stock-status");

        if (data.available) {

            span.innerHTML =
                " 🟢 " + data.quantity + " available";

        } else {

            span.innerHTML =
                " 🔴 Out of stock";
        }

        if (data.low_stock) {

            span.innerHTML +=
                " ⚠ Low stock";
        }
    });
}

</script>