<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use rpkamp\Behat\MailhogExtension\Context\MailhogAwareContext;
use rpkamp\Mailhog\MailhogClient;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

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
        $email = (new Email())
            ->from(new Address('me@myself.example', 'Myself'))
            ->to('me@myself.example')
            ->subject('Mailhog extension for Behat')
            ->text(
                'Check out this Behat extension for MailHog on
                 <a href="https://github.com/rpkamp/mailhog-behat-context" id="gh-id" title="gh-title" alt="gh-alt">github</a>.
                '
            );

        $mailer = new Mailer(new EsmtpTransport('localhost', 4025));

        $mailer->send($email);
    }
}
