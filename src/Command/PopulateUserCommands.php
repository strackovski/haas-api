<?php

namespace App\Command;

use App\Entity\HelpItem;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    /** @var EntityManagerInterface */
    private $em;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;


    /**
     * PopulateHelpCommand constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->em = $em;
        $this->encoder = $encoder;
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setDescription('Populate database with user accounts...');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = [
            [
                "id" => null,
                "username" => "sara.stojanovski@dlabs.si",
                "firstname" => "Sara",
                "lastname" => "Stojanovski",
            ],
            [
                "id" => null,
                "username" => "tim.sabanov@dlabs.si",
                "firstname" => "Tim",
                "lastname" => "Sabanov",
            ],
            [
                "id" => null,
                "username" => "ivan.romanovski@dlabs.si",
                "firstname" => "Ivan",
                "lastname" => "Romanovski"
            ],
            [
                "id" => null,
                "username" => "vladimir.strackovski@dlabs.si",
                "firstname" => "Vladimir",
                "lastname" => "Strackovski"
            ],
            [
                "id" => null,
                "username" => "eva.jersin@dlabs.si",
                "firstname" => "Eva",
                "lastname" => "Jersin"
            ]
        ];

        foreach ($users as $user) {

            if ($this->em->getRepository(User::class)->findOneBy(['username' => $user['username']])) {
                echo "Already exists..." . PHP_EOL;
                continue;
            }

            $u = new User();
            $u->setEmail($user['username']);
            $u->setUsername($user['username']);
            $u->setUsernameCanonical($user['username']);
            $u->setEmailCanonical($user['username']);
            $u->setFirstName($user['firstname']);
            $u->setLastName($user['lastname']);
            $u->setPassword($this->encoder->encodePassword($u, "test123"));
            $u->setEnabled(true);

            $this->em->persist($u);
            $this->em->flush();
        }






        }
}
