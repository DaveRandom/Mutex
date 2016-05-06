<?php declare(strict_types=1);

namespace Amp\Mutex;

use Amp\Promise;
use function Amp\resolve;

abstract class BaseMutex implements Mutex
{
    private function doWithLock(callable $callback): \Generator
    {
        /** @var Lock $lock */
        $lock = yield $this->getLock();

        try {
            $result = $callback();

            if ($result instanceof \Generator) {
                return yield from $result;
            }

            if ($result instanceof Promise) {
                return yield $result;
            }

            return $result;
        } finally {
            $lock->release();
        }
    }

    public function withLock(callable $callback): Promise
    {
        return resolve($this->doWithLock($callback));
    }

    abstract public function getLock(): Promise;
}
