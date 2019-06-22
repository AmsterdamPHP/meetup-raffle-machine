<?php
declare(strict_types=1);

namespace App\Controller;

use App\Raffle\CheckInService;
use App\Raffle\MeetupService;
use App\Raffle\RandomService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

final class EventController
{
    /**
     * @var MeetupService
     */
    private $meetupService;

    /**
     * @var RandomService
     */
    private $randomService;

    /**
     * @var CheckInService
     */
    private $checkInService;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        MeetupService $meetupService,
        RandomService $randomService,
        CheckInService $checkInService,
        Environment $twig
    ) {
        $this->meetupService = $meetupService;
        $this->randomService = $randomService;
        $this->checkInService = $checkInService;
        $this->twig = $twig;
    }

    /**
     * @Route(name="homepage", path="/", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $cacheBusting = filter_var($request->get('cache_busting', false), FILTER_VALIDATE_BOOLEAN);

        return new Response($this->twig->render(
            'index.html.twig',
            ['meetups' => $this->meetupService->getPresentAndPastEvents($cacheBusting)]
        ));
    }

    /**
     * @Route(name="event", path="/event/{id}", methods={"GET"})
     */
    public function event(string $id): Response
    {
        $event = $this->meetupService->getEvent($id);

        $checkins = $this->checkInService->getCheckInsForEvent($id);

        $winners = (count($checkins) > 0)? $this->randomService->getRandomNumbers(count($checkins)) : [];
        return new Response($this->twig->render(
            'event.html.twig',
            ['event' => $event, 'winners' => $winners, 'checkins' => $checkins]
        ));
    }

    /**
     * @Route(name="event_checkin", path="/event/{eventId}/checkin", methods={"GET", "POST"})
     */
    public function checkIn(Request $request, string $eventId): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $userId = $request->request->get('user_id');

            $this->checkInService->checkIn($eventId, $userId);

            return new JsonResponse(['result' => 'ok']);
        }

        $event = $this->meetupService->getEvent($eventId);
        $checkins = $this->checkInService->getCheckInsForEvent($eventId);

        return new Response($this->twig->render(
            'event_checkin.html.twig',
            ['event' => $event, 'checkins' => $checkins]
        ));
    }
}
