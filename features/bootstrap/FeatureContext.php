<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use rpkamp\Behat\MailhogExtension\Context\MailhogAwareContext;
use rpkamp\Mailhog\MailhogClient;

final class FeatureContext implements Context, MailhogAwareContext
{
    /**
     * @var MailhogClient
     */
    private $mailHog;

    public function setMailhog(MailhogClient $client): void
    {
        $this->mailHog = $client;
    }

    /**
     * @Given /^I sent an email with a link$/
     */
    public function iSentAnEmailWithALink(): void
    {
        $message = (new Swift_Message())
            ->setFrom('me@myself.example', 'Myself')
            ->setTo('me@myself.example')
            ->setBody(
                'Check out this Behat extension for MailHog on
                 <a href="https://github.com/rpkamp/mailhog-behat-context" id="gh-id" title="gh-title" alt="gh-alt">github</a>.
                ')
            ->setSubject('Mailhog extension for Behat');

        $mailer = new Swift_Mailer(new Swift_SmtpTransport('localhost', 3025));

        $mailer->send($message);
    }
}
