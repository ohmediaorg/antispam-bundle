<?php

namespace OHMedia\AntispamBundle\Form\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Util\ServerParams;
use Symfony\Component\HttpFoundation\RequestStack;

class AntispamValidationListener implements EventSubscriberInterface
{
    private ServerParams $serverParams;

    public function __construct(
        private RequestStack $requestStack,
        private Security $security,
        ?ServerParams $serverParams,
    ) {
        $this->serverParams = $serverParams ?: new ServerParams();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        $isMethodPost = 'POST' === $form->getConfig()->getMethod();

        if ($isMethodPost && $this->serverParams->hasPostMaxSizeBeenExceeded()) {
            return;
        }

        if (!$form->isRoot()) {
            return;
        }

        if (!$form->getConfig()->getOption('compound')) {
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
            $message = sprintf(
                'Spam prevention: please wait %s seconds before submitting again.',
                $diff,
            );

            $form->addError(new FormError($message));

            if (!$request->isXmlHttpRequest()) {
                $session->getFlashBag()->add('error', $message);
            }
        } else {
            // if 5 minutes have elapsed, reset the count to 1
            if ($diff < -300) {
                $count = 1;
            }

            $allowedAfter = $now + $count * 5;
        }

        $session->set($key, [
            'allowed_after' => $allowedAfter,
            'count' => $count,
        ]);
    }
}
