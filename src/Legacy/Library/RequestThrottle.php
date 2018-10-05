<?php

namespace Legacy\Library;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\HttpFoundation\Request;

/**
 * Naive request throttling library.
 */
class RequestThrottle
{

    /**
     * Cache storage, where we keep the
     * counter of requests per client.
     *
     * Note that this interface doesn't guarantee
     * atomic writes.
     */
    private $storage;

    /**
     * Upper request limit per $intervalSecs.
     */
    private $maxRequests;

    /**
     * Interval in seconds when the request counter
     * should be reseted.
     */
    private $intervalSecs;

    public function __construct(Cache $storage, $maxRequests = 50, $intervalSecs = 180)
    {
        $this->storage = $storage;
        $this->maxRequests = $maxRequests;
        $this->intervalSecs = $intervalSecs;
    }

    public function isThrottled(Request $request)
    {
        $ip = $request->getClientIp();
        $storageKey = $this->ip2Key($ip);

        // Should be atomic (but isn't) -->
        if ($this->storage->contains($storageKey)) {
            $counter = intval($this->storage->fetch($storageKey));
        } else {
            $counter = 0;
        }

        $counter++;
        $this->storage->save($storageKey, $counter, $this->intervalSecs);
        // <-- should be atomic (but isn't)

        return ($counter > $this->maxRequests);
    }

    /**
     * Maps ip to key used in $storage.
     *
     * So that we don't need to store client ips.
     */
    private function ip2Key($ip)
    {
        return 'throttle.' . md5($ip);
    }

}
