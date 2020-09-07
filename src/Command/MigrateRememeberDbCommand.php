<?php

declare(strict_types = 1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MigrateRememeberDbCommand extends Command
{
	protected static $defaultName = 'migrate:remember:db';
	
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
			->setDescription('Create a table for symfony remember me');
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
				'sql'     => 'CREATE TABLE `rememberme_token` (
				`series`   char(88)     UNIQUE PRIMARY KEY NOT NULL,
				`value`    char(88)     NOT NULL,
				`lastUsed` datetime     NOT NULL,
				`class`    varchar(100) NOT NULL,
				`username` varchar(200) NOT NULL
			);',
			]
		);
		
		$runSqlCmd->run($runSqlCmdInput, $output);
		
		return 0;
	}
}
