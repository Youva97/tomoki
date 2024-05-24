<?php

namespace App\Controller\Admin;

use App\Entity\Script;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ScriptCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Script::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setPageTitle('edit', function ($entity) {
                return 'Script Id: ' . $entity->getId();
            });
    }

    
/*     public function configureFields(string $pageName): iterable
    {
        return [
            TextareaField::new('script')->setLabel('script'),
        ];
    } */
    
}
