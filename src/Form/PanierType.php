<?php

namespace App\Form;

use App\Entity\Panier;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class PanierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('dateachat', DateTimeType::class, [
            //    'widget' => 'single_text',
            //    'required' => true,
            //])
            ->add('etat')
            //->add('TotalPrix')
            //->add('CreatedAt', DateTimeType::class, [
            //    'widget' => 'single_text',
            //    'required' => true,
            //])
            //->add('user', EntityType::class, [
            //    'class' => User::class,
            //    'choice_label' => 'nom',
            //])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Panier::class,
        ]);
    }
}
