<?php

namespace App\Controller\Admin;


use App\Entity\User;
// use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ArrayFilter;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $passwordHasher = $this->passwordHasher->hashPassword(
            $entityInstance,
            $entityInstance->getPassword()
        );
        $entityInstance->setPassword($passwordHasher);
        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setFormOptions(['validation_groups' => ['register']])
            ->setEntityLabelInPlural('Utilisateurs')
            ->setPageTitle('new', 'Créer un Utilisateur')
            ->setPageTitle('edit', function ($entity) {
                return 'Utilisateur d\'Id: ' . $entity->getId();
            })
            ->setPageTitle('detail', function ($entity) {
                return 'Utilisateur d\'Id: ' . $entity->getId();
            });
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('firstName')->setLabel('Prénom'),
            TextField::new('lastName')->setLabel('Nom'),
            EmailField::new('email'),
            TextField::new('password')->onlyWhenCreating()->setFormType(PasswordType::class)->setLabel('Mot de passe')->onlyWhenCreating()->setRequired(true),
            TextField::new('confirmPassword')->onlyWhenCreating()->setRequired(true)->setFormType(PasswordType::class),
            ChoiceField::new('roles')->setChoices(['Admin' => 'ROLE_ADMIN', 'Utilisateur' => 'ROLE_USER'])->allowMultipleChoices()
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add('index', 'detail')
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                $action
                    ->setIcon('fas fa-trash')
                    ->displayIf(static function ($entity) {
                        return !in_array('ROLE_ADMIN', $entity->getRoles());
                    });
                return $action;
            })
            ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
                $action
                    ->setIcon('fas fa-trash')
                    ->displayIf(static function ($entity) {
                        return !in_array('ROLE_ADMIN', $entity->getRoles());
                    });
                return $action;
            })
            ->update('index', Action::NEW, function (Action $action) {
                $action->setLabel('Créer un Utilisateur');
                return $action;
            });
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add(
                ArrayFilter::new('roles')
                    ->setChoices(['Admin' => 'ROLE_ADMIN', 'Utilisateur' => 'ROLE_USER'])
            );
    }
}
