<?php declare(strict_types=1);

namespace Amp\Mutex;

interface Lock
{
    public function release();
}
