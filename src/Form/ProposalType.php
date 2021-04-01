<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\CategoryAttribute;
use App\Entity\Proposal;
use App\Entity\Ressource;
use App\Entity\RessourceAttribute;
use App\Repository\CategoryRepository;
use App\Repository\RessourceRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProposalType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('need_or_ask', ChoiceType::class, array(
                'choices' => array(
                    'Offrir' => true,
                    'Demander' => false
                ),
                'expanded' => true,
                'multiple' => false,
                'label'=>' '
            ))
            ->add('location')
            ->add('quantity')
            ->add('proposalPicture', FileType::class, [
                'label' => 'Rajouter une photo pour la proposition',

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
            ]);

//            ->add('category', EntityType::class, [
//                'class' => Category::class,
//                'choice_label' => 'name',
//                'placeholder' => 'Selectionner la catégorie',
//                'mapped' => false,
//                'required' => false
//            ]);

//            ->add('need_or_ask', RadioType::class, [
//                'choices' => [
//                    'j\'offre' => true,
//                    'je demande' => false,
//                ],
//                'label'=>''
//            ]);


//        Système d'évenement:
//        $builder
//            ->get('category')->addEventListener(
//                FormEvents::POST_SUBMIT,
//                function (FormEvent $event) {
//                    $form = $event->getForm();
//                    $this->addRessourceField($form->getParent(), $form->getData());
//                }
//            );
    }


//        $builder->get('ressource')->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event)
//            {
//                $form = $event->getForm();
//                $builder = $form->getParent()->getConfig()->getFormFactory()->createNamedBuilder(
//                    'attributes',
//                    EntityType::class,
//                    null,
//                    [
//                        'class' => CategoryAttribute::class,
//                        'placeholder' => 'Bref',
//                        'auto_initialize' => false,
//                        // pour avoir seulement les attributs liés à la ressource selectionnée
//                        'choices' => $attributes->getCategoryAttributes(),
//                        'mapped' => false,
//                    ]
//                );
//
//            }
//        );
//            ->add('quantity', null, [
//                'label'=>'Quantité'
//            ])
//            ->add('location', null, [
//                'label'=>'Localisation'
//            ])

//            ->add('ressource', EntityType::class, [
//                'class' => Ressource::class,
//                'choice_label'=> 'name',
//            ])


    /**
     * Rajoute un champs ressource au formulaire
     * @param FormInterface $form
     * @param Category $category
     */
    private function addRessourceField(FormInterface $form, Category $category)
    {
//        foreach ($category->getRessources() as $key => $value) {

//            dump($value);
//            exit();

        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'ressource',
            EntityType::class,
            null,
            [
                'class' => Ressource::class,
                'placeholder' => 'Selectionner la ressource',
                'choices' => $category->getRessources(),
                'choice_label' => 'description',
                'mapped' => false,
                'required' => false,
                'auto_initialize' => false,
            ]
        );

        $form->add($builder->getForm());
//        }

//        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
//            'ressource',
//            EntityType::class,
//            null,
//            [
//                'class' => Ressource::class,
//                'placeholder' => 'Selectionner la ressource',
//                'mapped' => false,
//                'auto_initialize' => false,
//                // pour avoir seulement les ressources liées à la catégorie selectionnée
//                'choices' => $category->getRessources(),
//            ]
//        );


//        Lorque je soumets la ressource
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
//                je vais recuperer le formulaire et appeler la fonction addressourceattributes

                $form = $event->getForm();

                if($form->getData() !== null){
//                    dump($form->getData());
//                    dump(gettype($form->getData()));
                    $this->addRessourceAttribute($form->getParent(), $form->getData());
                }
            }
        );

        $form->add($builder->getForm());

    }


    /**
     * Genere les champs attributs de la ressource
     * @param FormInterface $form
     * @param Ressource $ressource
     */
    private function addRessourceAttribute(FormInterface $form, Ressource $ressource)
    {
        foreach($ressource->getCategory()->getCategoryAttributes() as $key => $value){
            $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
                'Attributs'.$key,
                TextType::class,
                null,
                [
                    'mapped' => false,
                    'auto_initialize' => false,
                    'label' => $value->getName(),
                    'attr' => array(
                        'placeholder' => 'remplir l\'attribut',
                    )
//                    'class' => CategoryAttribute::class,
//                    'placeholder' => 'remplir les attributs',
//                    'choices' => $value
                ]);

            $form->add($builder->getForm());
        }

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {

                $form = $event->getForm();

                if($form->getData() !== null){
                    dump($form->getData());
//                    dump(gettype($form->getData()));
//                    $this->addRessourceAttribute($form->getParent(), $form->getData());
                }
            }
        );

        $form->add($builder->getForm());





    }





//        -------------

//        $builder->get('ressource')->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event)
//            {
//                $form = $event->getForm();
//                $this->addRessourceField($form->getParent(), $form->getData());
////                dump($this);
//
//            }
//
//        );


//        $builder = $form->get($category->getRessources())->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event2)
//            {
//                $form = $event2->getForm();
//                $this->addRessourceAttribute($form->getParent(), $form->getData());
//            }
//        );
//
//        $form->add($builder->getForm());


//    /**
//     * @param FormInterface $form
//     * @param Ressource $ressource
//     */
//    private function addRessourceAttribute(FormInterface $form, Ressource $ressource)
//    {
//        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
//            'ressource',
//            EntityType::class,
//            null,
//            [
//                'class' => Ressource::class,
//                'placeholder' => 'Selectionner la ressource',
//                'auto_initialize' => false,
//                // pour avoir seulement les ressources liées à la catégorie selectionnée
//                'choices' => $category->getRessources(),
//            ]
//        );
//
//
//
//        $form->add($builder->getForm());
//
//    }

//    -------------------


//    /**
//     * @param FormInterface $form
//     * @param Ressource $ressource
//     */
//    private function addRessourceAttribute(FormInterface $form, Ressource $ressource)
//    {
//        dump($ressource->getCategory()->getCategoryAttributes());
//        $i=2;
//        foreach($ressource->getCategory()->getCategoryAttributes() as $key => $value) {
////            $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
////                'ressourceAttribute',
////                TextType::class,
////                null,
////                [
////                    'label' => $value->getName(),
////                    'auto_initialize' => false,
////                    'mapped' => false,
////                ]
////            );
////            dump($builder);
//
//            $builder->get('ressource')->addEventListener(
//                FormEvents::POST_SUBMIT,
//                function (FormEvent $event2)
//                {
//                    $form = $event2->getForm();
//                    $this->addRessourceAttribute($form->getParent(), $form->getData());
//                }
//            );

//            -----------

//            $name = preg_replace("/[^a-zA-Z0-9]/", "", $value->getName());
//            $formBuilder = $form->getConfig()->getFormFactory()->createNamedBuilder($i++, FormType::class, null);
//            $formBuilder
//                ->add($value->getName(), TextType::class,
//                [
//                    'label' => $value->getName(),
//                    'auto_initialize' => false,
//                    'mapped' => false,
//                ]);

//            $form->add($formBuilder);

//            $form->add($builder->getForm());
//        }
//    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Proposal::class,
        ]);
    }
}
