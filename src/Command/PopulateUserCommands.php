<?php

namespace App\Command;

use App\Entity\HelpItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PopulateHelpCommand
 *
 * @package App\Command
 * @author  Vladimir Strackovski
 */
class PopulateUserCommands extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'populate:users';

    /**
     *
     */
    protected function configure()
    {
        $this->setDescription('Populate database with user accounts...');
    }

    /** @var EntityManagerInterface */
    private $em;

    /**
     * PopulateHelpCommand constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
