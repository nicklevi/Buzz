<?php

namespace Buzz\Middleware\History;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Journal implements \Countable, \IteratorAggregate
{
    private $entries = array();
    private $limit = 10;

    /**
     * Records an entry in the journal.
     *
     * @param RequestInterface $request  The request
     * @param ResponseInterface $response The response
     * @param integer          $duration The duration in seconds
     */
    public function record(RequestInterface $request, ResponseInterface $response, $duration = null): void
    {
        $this->addEntry(new Entry($request, $response, $duration));
    }

    public function addEntry(Entry $entry): void
    {
        array_push($this->entries, $entry);
        $this->entries = array_slice($this->entries, $this->getLimit() * -1);
        end($this->entries);
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getLast(): ?Entry
    {
        $entry = end($this->entries);

        return $entry === false ? null: $entry;
    }

    public function getLastRequest(): RequestInterface
    {
        return $this->getLast()->getRequest();
    }

    public function getLastResponse(): ResponseInterface
    {
        return $this->getLast()->getResponse();
    }

    public function clear(): void
    {
        $this->entries = array();
    }

    public function count(): int
    {
        return count($this->entries);
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getIterator()
    {
        return new \ArrayIterator(array_reverse($this->entries));
    }
}