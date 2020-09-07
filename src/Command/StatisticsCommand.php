<?php

namespace App\Command;

use App\Service\Statistics;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class StatisticsCommand extends Command
{
	/**
	 * @var string
	 */
    protected static $defaultName = 'app:statistics';
	
	/**
	 * @var Statistics
	 */
	private $statisticsService;
	
	/**
	 * @var LoggerInterface
	 */
	private $logger;
	
	/**
	 * WeeklyStatisticsSendCommand constructor.
	 * @param  Statistics       $statisticsService
	 * @param  LoggerInterface  $logger
	 */
	public function __construct(Statistics $statisticsService, LoggerInterface $logger)
	{
		parent::__construct();
		$this->statisticsService = $statisticsService;
		$this->logger            = $logger;
	}
	
	/**
	 * @inheritDoc
	 */
    protected function configure()
    {
		$this->setDescription('Gathers weekly statistics and sends an email to the system administrator.');
		$this->setHelp(
			'This command gathers weekly and all time top viewed 10 posts and comments and emails the statistics to the system administrator email configured in the .env file as MAIL_TO parameter.'
		);
    }
	
	/**
	 * @param  InputInterface   $input
	 * @param  OutputInterface  $output
	 * @return int
	 * @throws TransportExceptionInterface
	 */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
		$this->statisticsService->sendWeeklyStatistics();
		$this->logger->info('Weekly statistics sent to sysadmin. Review in the database under week' . date('W'));
	
		return 0;
    }
}
