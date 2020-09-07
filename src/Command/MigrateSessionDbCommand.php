<?php

declare(strict_types = 1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MigrateSessionDbCommand extends Command
{
	protected static $defaultName = 'migrate:session:db';
	
	/**
	 * @var ParameterBagInterface
	 */
	private ParameterBagInterface $parameterBag;
	
	public function __construct(ParameterBagInterface $parameterBag, string $name = null)
	{
		parent::__construct($name);
		$this->parameterBag = $parameterBag;
	}
	
	protected function configure()
	{
		$this
			->setDescription('Create a table for symfony sessions');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->setHidden(true);
		
		$app = $this->getApplication();
		
		if ($this->parameterBag->get('kernel.environment') !== 'dev') {
			$output->write('Cannot run command in production.');
			
			return 0;
		}
		
		$runSqlCmd      = $app->find('doctrine:query:sql');
		$runSqlCmdInput = new ArrayInput(
			[
				'command' => 'doctrine:query:sql',
				'sql'     => 'CREATE TABLE `sessions` (
				`sess_id` VARCHAR(128) NOT NULL PRIMARY KEY,
				`sess_data` MEDIUMBLOB NOT NULL,
				`sess_time` INTEGER UNSIGNED NOT NULL,
				`sess_lifetime` INTEGER UNSIGNED NOT NULL
				) COLLATE utf8mb4_bin, ENGINE = InnoDB;',
			]
		);
		
		$runSqlCmd->run($runSqlCmdInput, $output);
		
		return 0;
	}
}
