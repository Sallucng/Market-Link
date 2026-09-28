<?php

namespace App\Support;

class OperatingDays implements \Stringable, \JsonSerializable, \ArrayAccess, \Countable, \IteratorAggregate
{
    protected array $days;

    public function __construct(array|string|null $days = null)
    {
        if (is_null($days)) {
            $this->days = [];
        } elseif (is_array($days)) {
            $this->days = array_values(array_filter($days));
        } else {
            $decoded = json_decode($days, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->days = array_values(array_filter($decoded));
            } else {
                $this->days = array_values(array_filter(array_map('trim', explode(',', (string) $days))));
            }
        }
    }

    public function __toString(): string
    {
        return implode(', ', $this->days);
    }

    public function jsonSerialize(): array
    {
        return $this->days;
    }

    public function toArray(): array
    {
        return $this->days;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->days[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->days[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->days[] = $value;
        } else {
            $this->days[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->days[$offset]);
    }

    public function count(): int
    {
        return count($this->days);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->days);
    }
}
