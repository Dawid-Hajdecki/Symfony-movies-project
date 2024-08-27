<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
				'attr' => array(
					'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-2/5 h-20 text-2xl outline-none text-center rounded min-w-40',
					'placeholder' => 'Enter title...'
				),
				'label' => false,
				'required' => false,
			])
            ->add('releaseYear', IntegerType::class, [
				'attr' => array(
					'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-2/5 h-20 text-2xl outline-none text-center rounded min-w-40',
					'placeholder' => 'Enter release year...'
				),
				'label' => false,
				'required' => false,
			])
            ->add('description', TextareaType::class, [
				'attr' => array(
					'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-2/5 h-20 text-2xl outline-none text-center rounded min-w-40',
					'placeholder' => 'Enter description...'
				),
				'label' => false,
				'required' => false,
			])
			->add('imagePath', FileType::class, [
				'required' => false,
				'mapped' => false,
				'label' => false,
				'attr' => array(
					'class' => 'block mt-10 mx-auto text-center min-w-40',
				)
			])
            //->add('imagePath', FileType::class, [
			//	'attr' => array(
			//		'class' => 'py-10'
			//	),
			//	'label' => false
			//])
            //->add('actors', EntityType::class, [
            //    'class' => Actor::class,
            //    'choice_label' => 'id',
            //    'multiple' => true,
            //])
        ;//
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
