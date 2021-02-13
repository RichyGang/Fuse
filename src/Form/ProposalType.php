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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProposalType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Selectionner la catégorie',
                'mapped' => false,
                'required' => false
            ]);


        //Système d'évenement:
        $builder
            ->get('category')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $this->addRessourceField($form->getParent(), $form->getData());
                }
            );
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
//            ->add('need_or_ask', ChoiceType::class, [
//                'choices' => [
//                    "j'offre" => true,
//                    'je demande' => false,
//                ],
//                'label'=>' '
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
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'ressource',
            EntityType::class,
            null,
            [
                'class' => Ressource::class,
                'placeholder' => 'Selectionner la ressource',
                'mapped' => false,
                'auto_initialize' => false,
                // pour avoir seulement les ressources liées à la catégorie selectionnée
                'choices' => $category->getRessources(),
            ]
        );


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
