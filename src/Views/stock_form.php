<?php $title = ($isEdit ? 'Edit' : 'Add') . ' Stock — PhoneBook'; ?>
<?php ob_start(); ?>

<div class="page-header">
    <div>
        <h1>
            <?= $isEdit ? 'Edit Stock' : 'New Stock' ?>
        </h1>
        <p class="subtitle">
            <?= $isEdit ? 'Update stock details' : 'Add a stock to your portfolio' ?>
        </p>
    </div>
    <a href="/stocks" class="btn btn-ghost">← Back</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <span class="alert-icon">⚠️</span>
        <ul class="error-list">
            <?php foreach ($errors as $err): ?>
                <li>
                    <?= htmlspecialchars($err) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" action="<?= $isEdit ? '/stocks/edit' : '/stocks/add' ?>" class="stock-form">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($stock->id) ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="symbol">Stock Symbol <span class="required">*</span></label>
                <input type="text" id="symbol" name="symbol" value="<?= htmlspecialchars($stock->symbol) ?>"
                    placeholder="AAPL" required maxlength="10" style="text-transform: uppercase;">
            </div>
            <div class="form-group">
                <label for="sector">Sector</label>
                <select id="sector" name="sector">
                    <?php
                    $sectors = ['General', 'Technology', 'Healthcare', 'Finance', 'Energy', 'Consumer', 'Industrial', 'Utilities', 'Real Estate'];
                    foreach ($sectors as $sec):
                        ?>
                        <option value="<?= $sec ?>" <?= ($stock->sector === $sec) ? 'selected' : '' ?>>
                            <?= $sec ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="name">Company Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($stock->name) ?>"
                placeholder="Apple Inc." required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="quantity">Quantity <span class="required">*</span></label>
                <input type="number" id="quantity" name="quantity" 
                       value="<?= $stock->quantity ? number_format($stock->quantity, 2, '.', '') : '' ?>"
                       placeholder="100" step="0.01" min="0.01" required>
            </div>
            <div class="form-group">
                <label for="purchase_price">Purchase Price <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-prefix">$</span>
                    <input type="number" id="purchase_price" name="purchase_price" 
                           value="<?= $stock->purchasePrice ? number_format($stock->purchasePrice, 2, '.', '') : '' ?>"
                           placeholder="150.00" step="0.01" min="0.01" required>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="current_price">Current Price <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-prefix">$</span>
                <input type="number" id="current_price" name="current_price" 
                       value="<?= $stock->currentPrice ? number_format($stock->currentPrice, 2, '.', '') : '' ?>"
                       placeholder="175.00" step="0.01" min="0" required>
            </div>
        </div>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" rows="3"
                placeholder="Additional notes about this stock..."><?= htmlspecialchars($stock->notes) ?></textarea>
        </div>

        <div class="form-actions">
            <a href="/stocks" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Save Changes' : 'Add Stock' ?>
            </button>
        </div>
    </form>
</div>

<style>
.stock-form textarea {
    resize: vertical;
    min-height: 80px;
    font-family: inherit;
}

.input-group {
    position: relative;
    display: flex;
    align-items: center;
}

.input-prefix {
    position: absolute;
    left: 0.75rem;
    color: var(--text-secondary);
    font-weight: 500;
    z-index: 1;
    pointer-events: none;
}

.input-group input {
    padding-left: 2rem;
}

#symbol {
    text-transform: uppercase;
}
</style>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>