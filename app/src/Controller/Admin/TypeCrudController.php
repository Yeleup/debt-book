<?php

namespace App\Controller\Admin;

use App\Entity\Type;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Type::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'type.title'),
            ColorField::new('color'),
            NumberField::new('sort'),
            ChoiceField::new('prefix', 'type.prefix')->setChoices(['type.minus' => '-', 'type.plus' => '+']),
            BooleanField::new('payment_status', 'type.payment_status')->onlyOnForms(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud)->setEntityPermission('ROLE_ADMIN');
        $crud->setDefaultSort(['sort' => 'ASC']);
        return parent::configureCrud($crud)->setEntityPermission('ROLE_ADMIN'); // TODO: Change the autogenerated stub
    }
}
