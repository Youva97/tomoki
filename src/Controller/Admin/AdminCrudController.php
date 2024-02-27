<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ArrayFilter;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminCrudController extends AbstractCrudController
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
        return $crud->setFormOptions(['validation_groups' => ['register']]);
    }


    public function configureActions(Actions $actions): Actions
    {

        $goToStripe = Action::new('goToStripe')
    ->linkToUrl('https://www.stripe.com/')
    ->createAsGlobalAction()
;

        return $actions


            ->add('index', 'detail')

            ->remove('index',action::BATCH_DELETE)

            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return
                    $action->setIcon('fa fa-trash')
                    ->displayIf(static function ($entity) {
                        foreach ($entity->getRoles() as $role) {
                            if ($role=='ROLE_ADMIN') return false;
                        }
                        return true;
                    }) ;
                return $action;
            })
            ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
                return
                    $action->setIcon('fa fa-trash')
                    ->displayIf(static function ($entity) {
                        foreach ($entity->getRoles() as $role) {
                            if ($role=='ROLE_ADMIN') return false;
                        }
                        return true;
                    }) ;
                return $action;
            })
            ;

    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('firstName')
            ->add('lastName')
            ->add(ArrayFilter::new('roles')->setChoices(['Admin' => 'ROLE_ADMIN', 'Utilisateur' => '']))
         
        ;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $reponse =  $this->container->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $reponse -> andwhere("entity.roles  LIKE '%ROLE_ADMIN%'");
        return $reponse;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('firstName')->setLabel('Prénom'),
            TextField::new('lastName')->setLabel('Nom'),
            EmailField::new('email'),
            TextField::new('password')->onlyWhenCreating()->setFormType(PasswordType::class),
            TextField::new('confirmPassword')->onlyWhenCreating()->setRequired(true)->setFormType(PasswordType::class),
            ChoiceField::new('roles')->setChoices(['Admin' => 'ROLE_ADMIN', 'Utilisateur' => 'ROLE_USER'])->allowMultipleChoices()

        ];
    }
}
