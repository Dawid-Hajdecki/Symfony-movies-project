<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserRootFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
		$builder
			->add('email', TextType::class, [
				'label' => false,
				'attr' => [
					'autocomplete' => 'email',
					'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-2/5 h-20 text-2xl outline-none text-center rounded min-w-40',
					'placeholder' => 'Email'
				],
			])
			->add('name', TextType::class, [
				'label' => false,
				'attr' => [
					'autocomplete' => 'email',
					'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-2/5 h-20 text-2xl outline-none text-center rounded min-w-40',
					'placeholder' => 'Name'
				],
			])
			->add('roles', ChoiceType::class, [
				'choices' => [
					'Admin' => 'ROLE_ADMIN'
				],
				'attr' => [
					'class' => 'bg-transparent block mt-10 mx-auto w-2/5 h-20 text-2xl outline-none text-center rounded min-w-40',
				],
				'label' => false,
				'expanded' => true,
				'multiple' => true,
			])
		;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
