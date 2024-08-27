<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileFormType extends AbstractType
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
					'autocomplete' => 'name',
					'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-2/5 h-20 text-2xl outline-none text-center rounded min-w-40',
					'placeholder' => 'Name'
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
