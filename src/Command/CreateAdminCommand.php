<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Validator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates an new admin user and store then in the database.',
)]
class CreateAdminCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly ArgumentsCommand $arguments,
        private readonly UserRepository $users,
        private readonly Validator $validator,
        private readonly UserPasswordHasherInterface $hasher,
    ){
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (null !== $input->getArgument('email') && null !== $input->getArgument('password')) return;

        $this->io->title('Add Admin Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:create-admin email@example.com password',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        $this->arguments->askArguments($input, 'email', $this->io);
        $this->arguments->askArguments($input, 'password', $this->io, true);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of new admin user to create')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password of new admin user to create')
            ->addOption('super-admin', null, InputOption::VALUE_NONE, 'if set, the super-admin is created as an super administrator')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('create-admin-command');

        /** @var string $email */
        $email = $input->getArgument('email');

        /** @var string $password */
        $password = $input->getArgument('password');

        /** @var bool $isSuperAdmin */
        $isSuperAdmin = $input->getOption('super-admin');

        $this->validateUserData($email, $password);

        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$isSuperAdmin ? User::ROLE_SUPER_ADMIN : User::ROLE_ADMIN]);
        $hashPassword = $this->hasher->hashPassword($user, $password);
        $user->setPassword($hashPassword);

        $this->users->create($user);

        $event = $stopwatch->stop('create-admin-command');
        if ($output->isVerbose()) {
            $this->io->comment(sprintf('New Admin database / Elapsed time: %.2f ms / Consumed memory: %.2f MB', $event->getDuration(), $event->getMemory() / (1024 ** 2)));
        }

        $this->io->success(sprintf('%s user was successfully created.', $isSuperAdmin ? 'Super Admin' : 'Admin'));

        return Command::SUCCESS;
    }

    private function validateUserData(string $email, string $password): void
    {
        $existingUser = $this->users->findOneBy(['email' => $email]);

        if (null !== $existingUser)
            throw new \RuntimeException(sprintf('There is already a user registered with the "%s" email.', $email));

        $this->validator->validateEmail($email);
        $this->validator->validatePassword($password);
    }
}
