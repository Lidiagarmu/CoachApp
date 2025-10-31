<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crea un usuario administrador por defecto si no existe, o actualiza su contraseña si ya existe.'
)]
class CreateAdminCommand extends Command
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $hasher;
    private string $adminEmail;
    private string $adminPassword;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        #[Autowire('%default_admin_email%')] string $adminEmail,
        #[Autowire('%default_admin_password%')] string $adminPassword
    ) {
        parent::__construct();
        $this->em = $em;
        $this->hasher = $hasher;
        $this->adminEmail = $adminEmail;
        $this->adminPassword = $adminPassword;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repo = $this->em->getRepository(User::class);

        $admin = $repo->findOneBy(['email' => $this->adminEmail]);

        // Validar contraseña mínima
        if (strlen($this->adminPassword) < 8) {
            $io->error('La contraseña por defecto debe tener al menos 8 caracteres. Cambia DEFAULT_ADMIN_PASSWORD.');
            return Command::FAILURE;
        }

        if (!$admin) {
            // Crear nuevo usuario admin
            $admin = new User();
            $admin->setEmail($this->adminEmail);
            $admin->setFullName('Administrador');
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword($this->hasher->hashPassword($admin, $this->adminPassword));

            $this->em->persist($admin);
            $this->em->flush();

            $io->success("Usuario administrador creado: {$this->adminEmail}");
        } else {
            // Actualizar la contraseña si ya existe
            $admin->setPassword($this->hasher->hashPassword($admin, $this->adminPassword));
            if (!in_array('ROLE_ADMIN', $admin->getRoles())) {
                $roles = $admin->getRoles();
                $roles[] = 'ROLE_ADMIN';
                $admin->setRoles(array_unique($roles));
            }
            $this->em->flush();

            $io->success("Usuario administrador existente actualizado: {$this->adminEmail}");
        }

        return Command::SUCCESS;
    }
}
