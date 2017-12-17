<?php
declare(strict_types=1);

namespace rpkamp\Behat\Context;

use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;
use Exception;
use rpkamp\Behat\MailhogExtension\Context\MailhogAwareContext;
use rpkamp\Mailhog\MailhogClient;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;

final class MinkAwareMailhogContext implements MailhogAwareContext, MinkAwareContext
{
    /**
     * @var Mink
     */
    private $mink;

    /**
     * @var array
     */
    private $minkParameters;

    /**
     * @var MailhogClient
     */
    private $mailhogClient;

    public function setMink(Mink $mink)
    {
        $this->mink = $mink;
    }

    public function setMinkParameters(array $parameters)
    {
        $this->minkParameters = $parameters;
    }

    public function setMailhog(MailhogClient $client)
    {
        $this->mailhogClient = $client;
    }

    /**
     * @When /^I click the link "([^"]*)" in the last received email$/
     */
    public function iClickTheLinkInTheLastReceivedEmail(string $link)
    {
        if (!class_exists(Crawler::class)) {
            throw new Exception(
                sprintf(
                    'In order for Mink integration to work in %s you need to install symfony/dom-crawler',
                    __CLASS__
                )
            );
        }

        $message = $this->mailhogClient->getLastMessage();

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

        $this->mink->getSession()->visit($filtered->eq(0)->attr('href'));
    }
}
