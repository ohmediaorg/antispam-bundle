<?php

namespace OHMedia\AntispamBundle\Validator;

use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\CreateAssessmentRequest;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use OHMedia\AntispamBundle\OHMediaAntispamBundle;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CaptchaValidator extends ConstraintValidator
{
    private $url;

    public function __construct(
        private KernelInterface $kernel,
        private RequestStack $requestStack,
        private string $projectId,
        private string $sitekey,
        private string $type,
    ) {
        if (OHMediaAntispamBundle::CAPTCHA_TYPE_HCAPTCHA === $type) {
            $this->url = 'https://hcaptcha.com/siteverify';
        }
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$value) {
            $this->context->addViolation($constraint->message);

            return;
        }

        $mainRequest = $this->requestStack->getMainRequest();
        $remoteip = $mainRequest->getClientIp();

        if (OHMediaAntispamBundle::CAPTCHA_TYPE_HCAPTCHA === $this->type) {
            $this->hcaptcha($value, $constraint, $remoteip);
        } elseif (OHMediaAntispamBundle::CAPTCHA_TYPE_RECAPTCHA === $this->type) {
            $this->recaptcha($value, $constraint, $remoteip);
        }
    }

    private function recaptcha(string $value, Constraint $constraint, string $remoteip): void
    {
        try {
            $credsPath = $this->kernel->getProjectDir().'/credentials.json';

            if (!file_exists($credsPath)) {
                throw new \Exception('Missing recaptcha credentials.json');
            }

            $client = new RecaptchaEnterpriseServiceClient([
                'credentials' => $credsPath,
            ]);

            $projectName = $client->projectName($this->projectId);

            $assessment = new Assessment([
                'event' => new Event([
                    'token' => $value,
                    'site_key' => $this->sitekey,
                    'user_ip_address' => $remoteip,
                    // TODO - Should review args this takes
                    // 'user_agent' => $userAgent,
                ]),
            ]);

            $request = (new CreateAssessmentRequest())
                ->setParent($projectName)
                ->setAssessment($assessment);

            $response = $client->createAssessment($request);

            // TODO - potential to enable this through config with value per client.
            // $score = $response->getRiskAnalysis()->getScore() ?? 0.0;
        } catch (\Exception $e) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
     * Validate hcaptcha.
     *
     * @throws \JsonException
     */
    // TODO - For simplicity I wonder if we just stick with recaptcha?
    private function hcaptcha(string $value, Constraint $constraint, string $remoteip): void
    {
        $opts = ['http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'secret' => $this->projectId,
                'response' => $value,
                'remoteip' => $remoteip,
            ]),
        ]];

        $context = stream_context_create($opts);

        $result = @file_get_contents($this->url, false, $context);

        if (!$result) {
            $this->context->addViolation($constraint->message);

            return;
        }

        $json = @json_decode($result);

        if (!$json->success) {
            $this->context->addViolation($constraint->message);
        }
    }
}
