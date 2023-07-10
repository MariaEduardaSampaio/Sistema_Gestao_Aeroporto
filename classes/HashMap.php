<?php
require_once "HashableAndEquatable.php";
require_once "HashableAndComparable.php";

/**
 * @template K of HashableAndEquatable|HashableAndComparable
 * @template V
 */
class HashMapEntry
{
    /**
     * @var K
     */
    public readonly HashableAndEquatable|HashableAndComparable $key;
    /**
     * @var V
     */
    public mixed $value;

    public function __construct(HashableAndEquatable|HashableAndComparable $key, mixed $value)
    {
        $this->key = $key;
        $this->value = $value;
    }
}

/**
 * @template K of HashableAndEquatable|HashableAndComparable
 * @template V
 */
class HashMap
{
    /**
     * @var array<int, array<int, HashMapEntry<K, V>>>
     */
    private array $buckets;
    private int $size;

    public function __construct()
    {
        $this->buckets = [];
        $this->size = 0;
    }

    /**
     * Adds a key-value pair to the map.
     *
     * @param K $key The key.
     * @param V $value The value.
     * @return V
     */
    public function put(HashableAndEquatable|HashableAndComparable $key, mixed $value): mixed
    {
        $bucketIndex = $this->getBucketIndex($key);
        if (!isset($this->buckets[$bucketIndex])) {
            $this->buckets[$bucketIndex] = [];
        }

        foreach ($this->buckets[$bucketIndex] as $entry) {
            if ($key->eq($entry->key)) {
                $entry->value = $value;
                return $value;
            }
        }

        $this->buckets[$bucketIndex][] = new HashMapEntry($key, $value);
        $this->size++;
        return $value;
    }

    /**
     * Retrieves the value associated with the specified key.
     *
     * @param K $key The key.
     * @return V|null The value associated with the key, or null if the key is not found.
     */
    public function get(HashableAndEquatable|HashableAndComparable $key): mixed
    {
        $bucketIndex = $this->getBucketIndex($key);
        if (isset($this->buckets[$bucketIndex])) {
            foreach ($this->buckets[$bucketIndex] as $entry) {
                if ($key->eq($entry->key)) {
                    return $entry->value;
                }
            }
        }

        return null;
    }

    /**
     * Removes the key-value pair associated with the specified key.
     *
     * @param K $key The key.
     * @return bool
     */
    public function remove(HashableAndEquatable|HashableAndComparable $key): bool
    {
        $bucketIndex = $this->getBucketIndex($key);
        if (isset($this->buckets[$bucketIndex])) {
            foreach ($this->buckets[$bucketIndex] as $index => $entry) {
                if ($key->eq($entry->key)) {
                    unset($this->buckets[$bucketIndex][$index]);
                    $this->size--;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks if the map contains the specified key.
     *
     * @param K $key The key.
     * @return bool True if the key is found, false otherwise.
     */
    public function containsKey(HashableAndEquatable|HashableAndComparable $key): bool
    {
        $bucketIndex = $this->getBucketIndex($key);
        if (isset($this->buckets[$bucketIndex])) {
            foreach ($this->buckets[$bucketIndex] as $entry) {
                if ($key->eq($entry->key)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Removes all entries from the map.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->buckets = [];
        $this->size = 0;
    }

    /**
     * Returns the number of entries in the map.
     *
     * @return int The size of the map.
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * Returns an array of all the entries in the map.
     *
     * @return HashMapEntry<K, V>[] An array of HashMapEntry objects.
     */
    public function entries(): array
    {
        $entries = [];
        foreach ($this->buckets as $bucket) {
            foreach ($bucket as $entry) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * Returns an array of all the keys in the map.
     *
     * @return K[] An array of HashableAndEquatable|HashableAndComparable objects representing the keys.
     */
    public function keys(): array
    {
        $keys = [];
        foreach ($this->buckets as $bucket) {
            foreach ($bucket as $entry) {
                $keys[] = $entry->key;
            }
        }

        return $keys;
    }

    /**
     * Returns an array of all the values in the map.
     *
     * @return V[] An array of values.
     */
    public function values(): array
    {
        $values = [];
        foreach ($this->buckets as $bucket) {
            foreach ($bucket as $entry) {
                $values[] = $entry->value;
            }
        }

        return $values;
    }


    private function getBucketIndex(HashableAndEquatable|HashableAndComparable $key): int
    {
        $hashCode = $key->hashCode();
        return crc32($hashCode) % PHP_INT_MAX;
    }
    public function __toString(): string {
        $s = "{\n";
        $first = true;
        foreach ($this->entries() as $kv) {
            if (!$first ) {
                $s = $s.",\n";
            }
            $s = '  '.$s.$kv->key.": ".$kv->value;
            $first = false;
        }
        $s = $s."\n}";
        return $s;
    }
}