<?php

namespace App\Command;

use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-admin',
    description: 'Deletes an admin user from the database.',
)]
class DeleteAdminCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly ArgumentsCommand $arguments,
        private readonly UserRepository $users,
        private readonly LoggerInterface $logger
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of admin user to delete')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if (null !== $input->getArgument('email')) return;

        $this->io->title('Remove Admin Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:remove-user email@example.com',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        $this->arguments->askArguments($input, 'email', $this->io);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $email */
        $email = $input->getArgument('email');

        /** @var $user */
        $user = $this->users->findOneBy(['email' => $email]);

        if (null === $user) throw new \RuntimeException(sprintf('Aucun utilisateur avec l\'email "%s".', $email));

        $userId = $user->getId();
        $userEmail = $user->getEmail();

        $this->users->delete($user);

        $this->io->success(sprintf('User (ID: "%d", email: %s) was successfully deleted.', $userId, $userEmail));
        $this->logger->info('User (ID: {id}, email: {email}) was successfully deleted.', ['id' => $userId, 'email' => $userEmail]);

        return Command::SUCCESS;
    }
}
