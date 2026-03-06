<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Command;

use Setono\SyliusFeedPlugin\Processor\FeedProcessorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('setono:sylius-feed:process', description: 'Processes all enabled feeds')]
final class ProcessFeedsCommand extends Command
{
    public function __construct(private readonly FeedProcessorInterface $feedProcessor)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->feedProcessor->setLogger(new ConsoleLogger($output));
        $this->feedProcessor->process();

        return self::SUCCESS;
    }
}
