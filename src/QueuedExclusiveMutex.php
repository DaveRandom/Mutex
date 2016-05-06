<?php declare(strict_types=1);

namespace Amp\Mutex;

use Amp\Promise;
use Amp\Deferred;
use function Amp\resolve;

class QueuedExclusiveMutex extends BaseMutex
{
    /**
     * @var Deferred
     */
    private $last;

    private function doGetLock(): \Generator
    {
        $deferred = new Deferred();
        $last = $this->last;

        $this->last = $deferred;

        if ($last !== null) {
            yield $last->promise();
        }

        return new class($deferred) implements Lock
        {
            private $deferred;

            private $released;

            public function __construct(Deferred $deferred)
            {
                $this->deferred = $deferred;
            }

            public function __destruct()
            {
                if (!$this->released) {
                    $this->release();
                }
            }

            public function release()
            {
                $this->deferred->succeed();
                $this->deferred = null; // remove our ref in case someone keeps their lock ref'd after they release
                $this->released = true;
            }
        };
    }

    public function getLock(): Promise
    {
        return resolve($this->doGetLock());
    }
}
