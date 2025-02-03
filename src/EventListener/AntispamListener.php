<?php

namespace OHMedia\AntispamBundle\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
// use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class AntispamListener
{
    private const BUFFER = 5; // seconds

    public function __construct(private Security $security)
    {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if ($request->isMethodSafe()) {
            return;
        }

        if ($this->security->isGranted('IS_AUTHENTICATED')) {
            // return;
        }

        $session = $request->getSession();

        $uri = $request->getRequestUri();

        $key = 'oh_media_antispam_'.str_replace('/', '_', $uri);

        $data = $session->get($key, []);

        $now = time();

        $count = $data['count'] ?? 1;

        $allowedAfter = $data['allowed_after'] ?? $now;

        $exception = null;

        if ($allowedAfter < $now) {
            $diff = $now - $allowedAfter;

            // if someone keeps trying to spam the form
            // this increases the time they have to wait
            ++$count;

            $exception = new ValidationFailedException(sprintf(
                'Anti-spam prevention: please wait %s seconds before making a new request.',
                $diff,
            ));
        }

        $session->set($key, [
            'allowed_after' => $now + self::BUFFER * $count,
            'count' => $count,
        ]);

        if ($exception) {
            throw $exception;
        }
    }
}
