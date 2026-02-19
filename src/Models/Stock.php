<?php
namespace PhpGuru\Models;

class Stock
{
    public string $id;
    public string $symbol;
    public string $name;
    public float $quantity;
    public float $purchasePrice;
    public float $currentPrice;
    public string $sector;
    public string $notes;
    public string $createdAt;

    public function __construct(
        string $symbol = '',
        string $name = '',
        float $quantity = 0,
        float $purchasePrice = 0,
        float $currentPrice = 0,
        string $sector = 'General',
        string $notes = '',
        string $id = '',
        string $createdAt = ''
    ) {
        $this->id = $id ?: uniqid('s_', true);
        $this->symbol = strtoupper($symbol);
        $this->name = $name;
        $this->quantity = $quantity;
        $this->purchasePrice = $purchasePrice;
        $this->currentPrice = $currentPrice;
        $this->sector = $sector;
        $this->notes = $notes;
        $this->createdAt = $createdAt ?: date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'symbol' => $this->symbol,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'purchasePrice' => $this->purchasePrice,
            'currentPrice' => $this->currentPrice,
            'sector' => $this->sector,
            'notes' => $this->notes,
            'createdAt' => $this->createdAt,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['symbol'] ?? '',
            $data['name'] ?? '',
            $data['quantity'] ?? 0,
            $data['purchasePrice'] ?? 0,
            $data['currentPrice'] ?? 0,
            $data['sector'] ?? 'General',
            $data['notes'] ?? '',
            $data['id'] ?? '',
            $data['createdAt'] ?? ''
        );
    }

    public function getTotalValue(): float
    {
        return $this->quantity * $this->currentPrice;
    }

    public function getTotalCost(): float
    {
        return $this->quantity * $this->purchasePrice;
    }

    public function getGainLoss(): float
    {
        return $this->getTotalValue() - $this->getTotalCost();
    }

    public function getGainLossPercentage(): float
    {
        if ($this->purchasePrice === 0) {
            return 0;
        }
        return (($this->currentPrice - $this->purchasePrice) / $this->purchasePrice) * 100;
    }

    public function isGainer(): bool
    {
        return $this->getGainLoss() > 0;
    }

    public function isLoser(): bool
    {
        return $this->getGainLoss() < 0;
    }
}