<?php declare(strict_types=1);

namespace Amp\Mutex;

use Amp\Promise;

interface Mutex
{
    /**
     * @param callable|\Generator $generator
     * @return Promise
     */
    public function withLock($generator): Promise;
    public function getLock(): Promise;
}
