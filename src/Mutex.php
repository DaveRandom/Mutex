<?php declare(strict_types=1);

namespace Amp\Mutex;

use Amp\Promise;

interface Mutex
{
    public function withLock(callable $callback): Promise;
    public function getLock(): Promise;
}
