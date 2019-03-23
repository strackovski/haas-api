<?php

namespace App\Command;

use App\Entity\HelpItem;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BlockchainSetupCommand
 *
 * @package App\Command
 * @author  Vladimir Strackovski
 */
class BlockchainSetupCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'bc:setup';
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
     *
     */
    protected function configure()
    {
        $this->setDescription('bc...');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->em->getRepository(User::class)->findAll();

        foreach ($users as $user) {

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://e17da4dc.ngrok.io/users/add/user/".$user->getUsername());
            $result = curl_exec($curl);

            print_r($result);
        }

        foreach ($users as $user) {

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://e17da4dc.ngrok.io/users/assets/deposit/id/'.$user->getUsername().'/5000');
            $result = curl_exec($curl);

            print_r($result);
        }
    }
}
