<?php

namespace App\Form;

use App\Entity\Properties;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PropertiesType extends AbstractType
{

    const SUCCESS = 'Guardado Exitosamente';
    const EXISTS = 'Ya Existe el Usuario';
    const DELETED = 'Registo Eliminado';
    const UPDATE_EX = 'El Registo No Existe';
    const UPDATE_SU = 'Actuaizado';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('name')
            ->add('description')
            ->add('brand')
            ->add('price')
            ->add('Guardar', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Properties::class,
        ]);
    }
}
