<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\CategoryAttribute;
use App\Entity\Proposal;
use App\Entity\Ressource;
use App\Entity\RessourceAttribute;
use http\Env\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use function Sodium\add;
use Symfony\Component\Form\ChoiceList\ChoiceList;

class RessourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Selectionner la catégorie',
            ]);

        $builder
            ->get('category')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $this->addCategoryAttribute($form->getParent(), $form->getData());
                }
            );
    }

//----------------------------

//        $builder
//            ->add('name', null, ['label'=>'Nom'])
//            ->add('description')
//
////            ->add('parent', EntityType::class, [
////                // looks for choices from this entity
////                'class' => Ressource::class,
////
////                // uses the User.username property as the visible option string
////                'choice_label' => 'name',
////
////
////                // used to render a select box, check boxes or radios
////                // 'multiple' => true,
////                // 'expanded' => true,
////            ])
////            ->add('submit', SubmitType::class);
//
//        ;

    /**
     * Genere les champs attributs de la catégorie
     * @param FormInterface $form
     * @param Category $category
     */
    private function addCategoryAttribute(FormInterface $form, Category $category)
    {
        foreach ($category->getCategoryAttributes() as $key => $value) {

            $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
                'ressource_attribute' . $key,
                TextType::class,
                null,
                [
                    'mapped' => false,
                    'auto_initialize' => false,
                    'label' => $value->getName(),
                    'attr' => array(
                        'placeholder' => 'remplir l\'attribut',
                    )
                ]);

            $form->add($builder->getForm());

//            $builder = $form
//                ->add('ressource_attribute'.$key, EntityType::class, [
//                    'class' => RessourceAttribute::class,
//                    'choice_label' => 'value',
//                    'mapped'=> 'false',
//                    'placeholder' => 'Remplir la velure',
//                ]);


        }

//        $builder->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event) {
//                $form = $event->getForm();
//                if($form->getData() !== null){
//                    dump($form->getData());
////                    dump(gettype($form->getData()));
////                    $this->addRestFormRessource($form->getParent(), $form->getData());
//                }
//            }
//        );

//        $form->add($builder->getForm());


        $form
            ->add('description')
            ->add('ressourcePicture', FileType::class, [
                'label' => 'Rajouter une photo pour la ressource',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please send valid image',
                    ])
                ],
            ])
        ;

    }

    /**
     * @param FormInterface $form
     */
    private function addRestFormRessource(FormInterface $form, string $string)
    {
        dump($string);
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'ressource_attribute' . $key,
            TextType::class,
            null,
            [
                'auto_initialize' => false,
                'label' => $value->getValue(),
                'attr' => array(
                    'placeholder' => 'remplir l\'attribut',
                ),
                'mapped' => 'false'
            ]);
        $form->add($builder->getForm());
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ressource::class,
        ]);
    }
}
