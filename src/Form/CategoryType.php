<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    const SUCCESS = 'Guardado Exitosamente';
    const EXISTS = 'Ya Existe el Usuario';
    const DELETED = 'Registo Eliminado';
    const UPDATE_EX = 'El Registo No Existe';
    const UPDATE_SU = 'Actuaizado';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('active')
            ->add('Save', SubmitType::class)
            // ->add('active', BooleanType::class)
            // ->add('createAt')
            // ->add('updateAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
