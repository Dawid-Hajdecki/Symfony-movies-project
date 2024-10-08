<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
				// instead of being set onto the object directly,
				// this is read and encoded in the controller
				'label' => false,
				'mapped' => false,
				'attr' => [
					'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-2/5 h-20 text-2xl outline-none text-center rounded min-w-40',
					'placeholder' => 'New Password',],
				'constraints' => [
					new NotBlank([
						'message' => 'Please enter a password',
					]),
					new Length([
						'min' => 6,
						'minMessage' => 'Your password should be at least {{ limit }} characters',
						// max length allowed by Symfony for security reasons
						'max' => 4096,
					]),
				],
			])
            ->add('currentPassword', PasswordType::class, [
				// instead of being set onto the object directly,
				// this is read and encoded in the controller
				'label' => false,
				'mapped' => false,
				'attr' => [
					'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-2/5 h-20 text-2xl outline-none text-center rounded min-w-40',
					'placeholder' => 'Current Password',],
				'constraints' => [
					new NotBlank([
						'message' => 'Please enter a password',
					]),
					new Length([
						'min' => 6,
						'minMessage' => 'Your password should be at least {{ limit }} characters',
						// max length allowed by Symfony for security reasons
						'max' => 4096,
					]),
				],
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
