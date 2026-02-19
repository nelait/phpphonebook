<?php
namespace PhpGuru\Controllers;

use PhpGuru\Models\Stock;
use PhpGuru\Storage\StockStorage;

class StockController
{
    private StockStorage $storage;

    public function __construct()
    {
        $this->storage = new StockStorage();
    }

    public function index(): void
    {
        $search = $_GET['search'] ?? '';
        $filter = $_GET['filter'] ?? '';
        $sector = $_GET['sector'] ?? '';
        
        if ($search) {
            $stocks = $this->storage->search($search);
        } elseif ($filter === 'gainers') {
            $stocks = $this->storage->getGainers();
        } elseif ($filter === 'losers') {
            $stocks = $this->storage->getLosers();
        } elseif ($sector) {
            $stocks = $this->storage->getBySector($sector);
        } else {
            $stocks = $this->storage->getAll();
        }
        
        // Sort stocks by gain/loss percentage (highest first)
        usort($stocks, function($a, $b) {
            return $b->getGainLossPercentage() <=> $a->getGainLossPercentage();
        });
        
        $sectors = $this->storage->getSectors();
        $portfolioValue = $this->storage->getPortfolioValue();
        $portfolioCost = $this->storage->getPortfolioCost();
        $portfolioGainLoss = $this->storage->getPortfolioGainLoss();
        $portfolioGainLossPercentage = $portfolioCost > 0 ? (($portfolioValue - $portfolioCost) / $portfolioCost) * 100 : 0;
        
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        
        require __DIR__ . '/../Views/stocks.php';
    }

    public function add(): void
    {
        $stock = new Stock();
        $errors = [];
        $isEdit = false;
        require __DIR__ . '/../Views/stock_form.php';
    }

    public function store(): void
    {
        $symbol = trim(strtoupper($_POST['symbol'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $quantity = (float)($_POST['quantity'] ?? 0);
        $purchasePrice = (float)($_POST['purchase_price'] ?? 0);
        $currentPrice = (float)($_POST['current_price'] ?? 0);
        $sector = trim($_POST['sector'] ?? 'General');
        $notes = trim($_POST['notes'] ?? '');

        $errors = $this->validate($symbol, $name, $quantity, $purchasePrice, $currentPrice);
        if (!empty($errors)) {
            $stock = new Stock($symbol, $name, $quantity, $purchasePrice, $currentPrice, $sector, $notes);
            $isEdit = false;
            require __DIR__ . '/../Views/stock_form.php';
            return;
        }

        $stock = new Stock($symbol, $name, $quantity, $purchasePrice, $currentPrice, $sector, $notes);
        $this->storage->save($stock);
        $_SESSION['success'] = 'Stock added successfully!';
        header('Location: /stocks');
        exit;
    }

    public function edit(): void
    {
        $id = $_GET['id'] ?? '';
        $stock = $this->storage->getById($id);
        if (!$stock) {
            header('Location: /stocks');
            exit;
        }
        $errors = [];
        $isEdit = true;
        require __DIR__ . '/../Views/stock_form.php';
    }

    public function update(): void
    {
        $id = $_POST['id'] ?? '';
        $symbol = trim(strtoupper($_POST['symbol'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $quantity = (float)($_POST['quantity'] ?? 0);
        $purchasePrice = (float)($_POST['purchase_price'] ?? 0);
        $currentPrice = (float)($_POST['current_price'] ?? 0);
        $sector = trim($_POST['sector'] ?? 'General');
        $notes = trim($_POST['notes'] ?? '');

        $errors = $this->validate($symbol, $name, $quantity, $purchasePrice, $currentPrice);
        if (!empty($errors)) {
            $stock = new Stock($symbol, $name, $quantity, $purchasePrice, $currentPrice, $sector, $notes, $id);
            $isEdit = true;
            require __DIR__ . '/../Views/stock_form.php';
            return;
        }

        // Get original stock to preserve creation date
        $originalStock = $this->storage->getById($id);
        $createdAt = $originalStock ? $originalStock->createdAt : date('Y-m-d H:i:s');
        
        $stock = new Stock($symbol, $name, $quantity, $purchasePrice, $currentPrice, $sector, $notes, $id, $createdAt);
        $this->storage->save($stock);
        $_SESSION['success'] = 'Stock updated successfully!';
        header('Location: /stocks');
        exit;
    }

    public function delete(): void
    {
        $id = $_GET['id'] ?? '';
        $this->storage->delete($id);
        $_SESSION['success'] = 'Stock deleted successfully!';
        header('Location: /stocks');
        exit;
    }

    private function validate(string $symbol, string $name, float $quantity, float $purchasePrice, float $currentPrice): array
    {
        $errors = [];
        
        if (empty($symbol)) {
            $errors[] = 'Stock symbol is required.';
        } elseif (strlen($symbol) > 10) {
            $errors[] = 'Stock symbol must be 10 characters or less.';
        }
        
        if (empty($name)) {
            $errors[] = 'Stock name is required.';
        }
        
        if ($quantity <= 0) {
            $errors[] = 'Quantity must be greater than zero.';
        }
        
        if ($purchasePrice <= 0) {
            $errors[] = 'Purchase price must be greater than zero.';
        }
        
        if ($currentPrice < 0) {
            $errors[] = 'Current price cannot be negative.';
        }
        
        return $errors;
    }
}