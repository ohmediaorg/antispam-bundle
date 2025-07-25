<?php

namespace OHMedia\AntispamBundle\Form\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Util\ServerParams;
use Symfony\Component\HttpFoundation\RequestStack;

class ThrottleValidationListener implements EventSubscriberInterface
{
    private ServerParams $serverParams;

    public function __construct(
        private RequestStack $requestStack,
        private Security $security,
        private int $throttleTime,
        private int $throttleWindow,
        ?ServerParams $serverParams,
    ) {
        if ($throttleWindow < 0) {
            throw new \ValueError('$throttleWindow should be a positive integer.');
        }

        if ($this->throttleTime >= $throttleWindow) {
            throw new \ValueError('$throttleTime should be less than $throttleWindow.');
        }

        $this->serverParams = $serverParams ?: new ServerParams();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        $method = $form->getConfig()->getMethod();

        if ('GET' === $method) {
            return;
        }

        $isMethodPost = 'POST' === $method;

        if ($isMethodPost && $this->serverParams->hasPostMaxSizeBeenExceeded()) {
            return;
        }

        if (!$form->isRoot()) {
            return;
        }

        if (!$form->getConfig()->getOption('compound')) {
            return;
        }

        if (!$form->isValid()) {
            return;
        }

        if ($this->security->isGranted('IS_AUTHENTICATED')) {
            return;
        }

        $request = $this->requestStack->getMainRequest();

        $session = $request->getSession();

        $key = self::class;

        $data = $session->get($key, []);

        $now = time();

        $count = $data['count'] ?? 0;

        $allowedAfter = $data['allowed_after'] ?? $now;

        $diff = $allowedAfter - $now;

        ++$count;

        if ($diff > 0) {
            $noun = 1 === $diff ? 'second' : 'seconds';

            $message = sprintf(
                'Spam prevention: please wait %s %s before submitting again.',
                $diff,
                $noun,
            );

            $form->addError(new FormError($message));

            if (!$request->isXmlHttpRequest()) {
                $session->getFlashBag()->add('error', $message);
            }
        } else {
            // if the window has elapsed, reset the count to 1
            if ($diff < -$this->throttleWindow) {
                $count = 1;
            }

            $allowedAfter = $now + $count * $this->throttleTime;
        }

        $session->set($key, [
            'allowed_after' => $allowedAfter,
            'count' => $count,
        ]);
    }
}
