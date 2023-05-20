<?php
declare(strict_types=1);

namespace rpkamp\Behat\Context;

use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;
use Exception;
use rpkamp\Behat\MailhogExtension\Context\MailhogAwareContext;
use rpkamp\Behat\MailhogExtension\Context\OpenedEmailStorageAwareContext;
use rpkamp\Behat\MailhogExtension\Service\OpenedEmailStorage;
use rpkamp\Mailhog\MailhogClient;
use rpkamp\Mailhog\Message\Message;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

use function class_exists;
use function sprintf;

final class MinkAwareMailhogContext implements MailhogAwareContext, MinkAwareContext, OpenedEmailStorageAwareContext
{
    private Mink $mink;
    private MailhogClient $mailhogClient;
    private OpenedEmailStorage $openedEmailStorage;

    public function setMink(Mink $mink): void
    {
        $this->mink = $mink;
    }

    /**
     * @param mixed[] $parameters
     */
    public function setMinkParameters(array $parameters): void
    {
    }

    public function setMailhog(MailhogClient $client): void
    {
        $this->mailhogClient = $client;
    }

    public function setOpenedEmailStorage(OpenedEmailStorage $storage): void
    {
        $this->openedEmailStorage = $storage;
    }

    /**
     * @When /^I click the link "([^"]*)" in the last received email$/
     */
    public function iClickTheLinkInTheLastReceivedEmail(string $link): void
    {
        $this->clickLink($this->mailhogClient->getLastMessage(), $link);
    }

    /**
     * @When /^I click the link "([^"]*)" in the opened email$/
     */
    public function iClickTheLinkInTheOpenedEmail(string $link): void
    {
        if (!$this->openedEmailStorage->hasOpenedEmail()) {
            throw new RuntimeException('Unable to click link in opened email - no email has been opened yet');
        }

        $this->clickLink($this->openedEmailStorage->getOpenedEmail(), $link);
    }

    private function clickLink(Message $message, string $link): void
    {
        if (!class_exists(Crawler::class)) {
            throw new Exception(
                sprintf(
                    'In order for Mink integration to work in %s you need to install symfony/dom-crawler',
                    self::class
                )
            );
        }

        $crawler = new Crawler($message->body);

        $xPaths = [
            sprintf('//a[@id="%s"]', $link),
            sprintf('//a[text()="%s"]', $link),
            sprintf('//a[@title="%s"]', $link),
            sprintf('//a[@alt="%s"]', $link),
            sprintf('//a[contains(text(), "%s")]', $link),
        ];

        foreach ($xPaths as $xPath) {
            $filtered = $crawler->filterXPath($xPath);
            if ($filtered->getNode(0)) {
                break;
            }
        }

        if (!$filtered->count()) {
            throw new RuntimeException(sprintf('No link found with id|title|alt|text "%s"', $link));
        }

        $url = $filtered->eq(0)->attr('href');
        if (null === $url) {
            throw new RuntimeException(
                sprintf(
                    'Link with id|title|alt|text "%s" found, but missing "href" attribute',
                    $link
                )
            );
        }

        $this->mink->getSession()->visit($url);
    }
}
