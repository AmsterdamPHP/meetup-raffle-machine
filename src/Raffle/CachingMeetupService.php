<?php
declare(strict_types=1);

namespace App\Raffle;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class CachingMeetupService implements MeetupServiceInterface
{
    private const ONE_HOUR = 3600;

    /**
     * @var MeetupServiceInterface
     */
    private $inner;

    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        MeetupServiceInterface $inner,
        CacheItemPoolInterface $cacheItemPool,
        RequestStack $requestStack
    ) {
        $this->inner = $inner;
        $this->cacheItemPool = $cacheItemPool;
        $this->requestStack = $requestStack;
    }

    public function getPresentAndPastEvents(): array
    {
        $bustCache = $this->requestStack->getMasterRequest()->query->get('cache_busting', false);

        $cachedEvents = $this->cacheItemPool->getItem('events_cache');
        if (!$bustCache && $cachedEvents->isHit()) {
            return $cachedEvents->get();
        }

        $events = $this->inner->getPresentAndPastEvents();

        $cachedEvents->set($events);
        $cachedEvents->expiresAfter(self::ONE_HOUR);
        $this->cacheItemPool->save($cachedEvents);

        return $events;
    }

    public function getEvent(string $id): array
    {
        $cachedEvent = $this->cacheItemPool->getItem('event_cache_'.$id);
        if ($cachedEvent->isHit()) {
            return $cachedEvent->get();
        }

        $event = $this->inner->getEvent($id);

        $cachedEvent->set($event);
        $cachedEvent->expiresAfter(self::ONE_HOUR);
        $this->cacheItemPool->save($cachedEvent);

        return $event;
    }
}
