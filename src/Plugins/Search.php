<?php

namespace Zlt\Airtable\Plugins;

class Search
{
    protected array $whereNot = [];

    protected array $whereOr = [];

    protected array $where = [];

    protected array $and = [];

    public function __construct(protected ?\Closure $closure = null)
    {
    }

    public function and(\Closure $closure): static
    {
        $this->and[] = $closure(new static());
        return $this;
    }

    public function where(string $column, string|int $value): static
    {
        $this->where[$column] = $value;
        return $this;
    }

    public function whereNot(string $column, string|int $value): static
    {
        $this->whereNot[$column] = $value;
        return $this;
    }

    public function whereOr(string $column, string|int $value): static
    {
        $this->whereOr[$column] = $value;
        return $this;
    }

    protected function getFormula(): string
    {
        $formula = 'AND(';
        foreach ($this->where as $column => $value) {
            $formula .= "{" . $column . "}='$value'";
            if ($column === array_key_last($this->where) && empty($this->whereNot)) {
                continue;
            }
            $formula .= ',';
        }
        foreach ($this->whereNot as $column => $value) {
            $formula .= "NOT({" . $column . "}='$value')";
            if ($column === array_key_last($this->whereNot) && empty($this->whereOr)) {
                continue;
            }
            $formula .= ',';
        }
        if (!empty($this->whereOr)) {
            foreach ($this->whereOr as $column => $value) {
                $formula .= "OR({" . $column . "}='$value')";
                if ($column !== array_key_last($this->whereOr)) {
                    $formula .= ',';
                }
            }
        }
        $formula .= ')';
        if (!empty($this->and)) {
            $andFormula = 'AND(';
            foreach ($this->and as $and) {
                if ($and instanceof static) {
                    $andFormula .= $and->getFormula();
                    if ($and !== end($this->and)) {
                        $andFormula .= ',';
                    }
                }
            }
            $formula = $andFormula . ',' . $formula . ')';
        }
        return $formula;
    }

    public function get()
    {
        if (!$this->closure) {
            throw new \Exception('Closure is not defined');
        }
        return ($this->closure)($this->getFormula());
    }
}
