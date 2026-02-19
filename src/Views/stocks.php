<?php $title = 'Stock Portfolio — PhoneBook'; ?>
<?php ob_start(); ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <span class="alert-icon">✅</span>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1>Stock Portfolio</h1>
        <p class="subtitle">
            <?= count($stocks) ?> stock
            <?= count($stocks) !== 1 ? 's' : '' ?> • 
            Portfolio Value: $<?= number_format($portfolioValue, 2) ?>
        </p>
    </div>
    <a href="/stocks/add" class="btn btn-primary">
        <span>＋</span> Add Stock
    </a>
</div>

<!-- Portfolio Overview -->
<div class="portfolio-overview">
    <div class="overview-card">
        <h3>Total Value</h3>
        <div class="value">$<?= number_format($portfolioValue, 2) ?></div>
    </div>
    <div class="overview-card">
        <h3>Total Cost</h3>
        <div class="value">$<?= number_format($portfolioCost, 2) ?></div>
    </div>
    <div class="overview-card">
        <h3>Gain/Loss</h3>
        <div class="value <?= $portfolioGainLoss >= 0 ? 'positive' : 'negative' ?>">
            <?= $portfolioGainLoss >= 0 ? '+' : '' ?>$<?= number_format($portfolioGainLoss, 2) ?>
        </div>
        <div class="percentage <?= $portfolioGainLoss >= 0 ? 'positive' : 'negative' ?>">
            (<?= $portfolioGainLoss >= 0 ? '+' : '' ?><?= number_format($portfolioGainLossPercentage, 2) ?>%)
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="filter-bar">
    <form method="GET" action="/stocks" class="search-bar">
        <input type="text" name="search" placeholder="Search by symbol, name, or sector..."
            value="<?= htmlspecialchars($search ?? '') ?>" class="search-input">
        <button type="submit" class="btn btn-secondary">Search</button>
        <?php if (!empty($search)): ?>
            <a href="/stocks" class="btn btn-ghost">Clear</a>
        <?php endif; ?>
    </form>
    
    <div class="stock-filters">
        <a href="/stocks" class="filter-btn <?= empty($filter) && empty($sector) ? 'active' : '' ?>">All</a>
        <a href="/stocks?filter=gainers" class="filter-btn <?= ($filter ?? '') === 'gainers' ? 'active' : '' ?>">Gainers</a>
        <a href="/stocks?filter=losers" class="filter-btn <?= ($filter ?? '') === 'losers' ? 'active' : '' ?>">Losers</a>
    </div>

    <?php if (!empty($sectors)): ?>
        <div class="sector-filter">
            <label for="sector-select">Sector:</label>
            <select id="sector-select" onchange="filterBySector(this.value)">
                <option value="">All Sectors</option>
                <?php foreach ($sectors as $sec): ?>
                    <option value="<?= htmlspecialchars($sec) ?>" <?= ($sector ?? '') === $sec ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sec) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>
</div>

<!-- Stocks List -->
<?php if (empty($stocks)): ?>
    <div class="empty-state">
        <span class="empty-icon">📈</span>
        <h2>No stocks found</h2>
        <p>
            <?php if (!empty($search)): ?>
                Try a different search term.
            <?php elseif (!empty($filter)): ?>
                No <?= htmlspecialchars($filter) ?> found.
            <?php elseif (!empty($sector)): ?>
                No stocks found in this sector.
            <?php else: ?>
                Add your first stock to start building your portfolio!
            <?php endif; ?>
        </p>
        <?php if (empty($search) && empty($filter) && empty($sector)): ?>
            <a href="/stocks/add" class="btn btn-primary">＋ Add Stock</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="stocks-list">
        <?php foreach ($stocks as $stock): ?>
            <div class="stock-card <?= $stock->isGainer() ? 'gainer' : ($stock->isLoser() ? 'loser' : '') ?>">
                <div class="stock-header">
                    <div class="stock-info">
                        <div class="stock-symbol"><?= htmlspecialchars($stock->symbol) ?></div>
                        <div class="stock-name"><?= htmlspecialchars($stock->name) ?></div>
                        <div class="stock-sector">
                            <span class="sector-badge sector-<?= strtolower(str_replace(' ', '-', $stock->sector)) ?>">
                                <?= htmlspecialchars($stock->sector) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="stock-metrics">
                        <div class="metric">
                            <span class="metric-label">Quantity</span>
                            <span class="metric-value"><?= number_format($stock->quantity, 2) ?></span>
                        </div>
                        <div class="metric">
                            <span class="metric-label">Current Price</span>
                            <span class="metric-value">$<?= number_format($stock->currentPrice, 2) ?></span>
                        </div>
                        <div class="metric">
                            <span class="metric-label">Purchase Price</span>
                            <span class="metric-value">$<?= number_format($stock->purchasePrice, 2) ?></span>
                        </div>
                    </div>
                    
                    <div class="stock-performance">
                        <div class="total-value">$<?= number_format($stock->getTotalValue(), 2) ?></div>
                        <div class="gain-loss <?= $stock->getGainLoss() >= 0 ? 'positive' : 'negative' ?>">
                            <?= $stock->getGainLoss() >= 0 ? '+' : '' ?>$<?= number_format($stock->getGainLoss(), 2) ?>
                        </div>
                        <div class="percentage <?= $stock->getGainLoss() >= 0 ? 'positive' : 'negative' ?>">
                            (<?= $stock->getGainLoss() >= 0 ? '+' : '' ?><?= number_format($stock->getGainLossPercentage(), 2) ?>%)
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($stock->notes)): ?>
                    <div class="stock-notes"><?= htmlspecialchars($stock->notes) ?></div>
                <?php endif; ?>
                
                <div class="stock-actions">
                    <a href="/stocks/edit?id=<?= urlencode($stock->id) ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <a href="/stocks/delete?id=<?= urlencode($stock->id) ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete <?= htmlspecialchars(addslashes($stock->symbol)) ?>?')">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function filterBySector(sector) {
    const url = new URL(window.location);
    if (sector) {
        url.searchParams.set('sector', sector);
    } else {
        url.searchParams.delete('sector');
    }
    url.searchParams.delete('search');
    url.searchParams.delete('filter');
    window.location = url;
}
</script>

<style>
.portfolio-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.overview-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1.5rem;
    text-align: center;
}

.overview-card h3 {
    margin: 0 0 0.5rem 0;
    font-size: 0.875rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.overview-card .value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.overview-card .percentage {
    font-size: 0.875rem;
    font-weight: 500;
}

.positive {
    color: #16a34a;
}

.negative {
    color: #dc2626;
}

.filter-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    align-items: end;
}

.stock-filters {
    display: flex;
    gap: 0.5rem;
}

.filter-btn {
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.filter-btn:hover {
    color: var(--text-primary);
    border-color: var(--primary-color);
}

.filter-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.sector-filter label {
    display: block;
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.sector-filter select {
    padding: 0.5rem;
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    background: var(--bg-color);
    color: var(--text-primary);
    font-size: 0.875rem;
}

.stocks-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stock-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1.5rem;
    transition: all 0.2s ease;
}

.stock-card.gainer {
    border-left: 4px solid #16a34a;
}

.stock-card.loser {
    border-left: 4px solid #dc2626;
}

.stock-card:hover {
    border-color: var(--primary-color);
    box-shadow: 0 2px 8px var(--shadow-color);
}

.stock-header {
    display: flex;
    gap: 1.5rem;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.stock-info {
    flex-shrink: 0;
    min-width: 200px;
}

.stock-symbol {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.stock-name {
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.sector-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.sector-general { background: #e3f2fd; color: #1565c0; }
.sector-technology { background: #f3e5f5; color: #7b1fa2; }
.sector-healthcare { background: #e8f5e8; color: #2e7d32; }
.sector-finance { background: #ffebee; color: #c62828; }
.sector-energy { background: #fff3e0; color: #ef6c00; }
.sector-consumer { background: #fce4ec; color: #c2185b; }
.sector-industrial { background: #f1f8e9; color: #558b2f; }
.sector-utilities { background: #e0f2f1; color: #00796b; }

.stock-metrics {
    flex: 1;
    display: flex;
    gap: 2rem;
    justify-content: center;
}

.metric {
    text-align: center;
}

.metric-label {
    display: block;
    font-size: 0.75rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.metric-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
}

.stock-performance {
    flex-shrink: 0;
    text-align: right;
    min-width: 120px;
}

.total-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.gain-loss {
    font-weight: 600;
    margin-bottom: 0.125rem;
}

.stock-notes {
    background: var(--bg-secondary);
    padding: 0.75rem;
    border-radius: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

.stock-actions {
    display: flex;
    gap: 0.5rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-light);
}

@media (max-width: 768px) {
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .stock-filters {
        order: -1;
    }
    
    .stock-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .stock-metrics {
        gap: 1rem;
    }
    
    .stock-performance {
        text-align: left;
    }
    
    .stock-actions {
        flex-direction: column;
    }
}
</style>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>