<?php

namespace Larapulse\SitemapBundle\Tests\Controller;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Larapulse\SitemapBundle\Command\GenerateSitemapCommand;

class GenerateSitemapCommandTest extends WebTestCase
{
    public function testSitemapNbUrls()
    {
        $kernel = self::createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new GenerateSitemapCommand());

        $command = $application->find('sitemap:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('`http://www.google.de`', $commandTester->getDisplay());
        $this->assertRegExp('`http://github.com`', $commandTester->getDisplay());
    }
}
