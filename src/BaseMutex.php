<?php declare(strict_types=1);

namespace Amp\Mutex;

use Amp\Promise;
use function Amp\resolve;

abstract class BaseMutex implements Mutex
{
    protected function executeCoroutineWithLock(\Generator $generator): Promise
    {
        return resolve(function() use($generator) {
            /** @var Lock $lock */
            $lock = yield $this->getLock();

            try {
                return yield from $generator;
            } finally {
                $lock->release();
            }
        });
    }

    public function withLock($generator): Promise
    {
        if (!$generator instanceof \Generator) {
            if (!is_callable($generator)) {
                throw new \InvalidArgumentException('Locked routine must be callable or instance of Generator');
            }

            $generator = $generator();

            if (!$generator instanceof \Generator) {
                throw new \LogicException('Locked routine callable must return instance of Generator');
            }
        }

        return resolve($this->executeCoroutineWithLock($generator));
    }

    abstract public function getLock(): Promise;
}
