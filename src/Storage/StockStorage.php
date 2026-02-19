<?php
namespace PhpGuru\Storage;

use PhpGuru\Models\Stock;

class StockStorage
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/../../data/stocks.json';
        if (!file_exists($this->filePath)) {
            if (!is_dir(dirname($this->filePath))) {
                mkdir(dirname($this->filePath), 0755, true);
            }
            file_put_contents($this->filePath, '[]');
        }
    }

    /** @return Stock[] */
    public function getAll(): array
    {
        $data = json_decode(file_get_contents($this->filePath), true) ?: [];
        return array_map(fn($item) => Stock::fromArray($item), $data);
    }

    public function getById(string $id): ?Stock
    {
        foreach ($this->getAll() as $stock) {
            if ($stock->id === $id) {
                return $stock;
            }
        }
        return null;
    }

    public function save(Stock $stock): void
    {
        $stocks = $this->getAll();
        $found = false;
        foreach ($stocks as $key => $existing) {
            if ($existing->id === $stock->id) {
                $stocks[$key] = $stock;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $stocks[] = $stock;
        }
        $this->writeAll($stocks);
    }

    public function delete(string $id): void
    {
        $stocks = $this->getAll();
        $stocks = array_values(array_filter($stocks, fn($s) => $s->id !== $id));
        $this->writeAll($stocks);
    }

    /** @return Stock[] */
    public function search(string $query): array
    {
        $query = strtolower($query);
        return array_values(array_filter($this->getAll(), function ($s) use ($query) {
            return str_contains(strtolower($s->symbol), $query)
                || str_contains(strtolower($s->name), $query)
                || str_contains(strtolower($s->sector), $query)
                || str_contains(strtolower($s->notes), $query);
        }));
    }

    /** @return Stock[] */
    public function getBySector(string $sector): array
    {
        return array_values(array_filter($this->getAll(), fn($s) => $s->sector === $sector));
    }

    /** @return Stock[] */
    public function getGainers(): array
    {
        return array_values(array_filter($this->getAll(), fn($s) => $s->isGainer()));
    }

    /** @return Stock[] */
    public function getLosers(): array
    {
        return array_values(array_filter($this->getAll(), fn($s) => $s->isLoser()));
    }

    /** @return array */
    public function getSectors(): array
    {
        $sectors = [];
        foreach ($this->getAll() as $stock) {
            if (!in_array($stock->sector, $sectors)) {
                $sectors[] = $stock->sector;
            }
        }
        sort($sectors);
        return $sectors;
    }

    public function getPortfolioValue(): float
    {
        return array_sum(array_map(fn($s) => $s->getTotalValue(), $this->getAll()));
    }

    public function getPortfolioCost(): float
    {
        return array_sum(array_map(fn($s) => $s->getTotalCost(), $this->getAll()));
    }

    public function getPortfolioGainLoss(): float
    {
        return $this->getPortfolioValue() - $this->getPortfolioCost();
    }

    private function writeAll(array $stocks): void
    {
        $data = array_map(fn($s) => $s->toArray(), $stocks);
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}