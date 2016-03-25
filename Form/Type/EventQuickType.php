<?php

namespace Flower\PlannerBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

class EventQuickType extends AbstractType
{

    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description', 'ckeditor', array(
                'required' => false,
                'config_name' => 'minimal'
            ))
            ->add('status', null, array('required' => false))
            ->add('visible', 'choice', array(
                'choices' => array('Private' => 0, 'Company' => 1, 'Public' => 2),
                'choices_as_values' => true,
            ))
            ->add('contacts', 'tetranz_select2entity', [
                'multiple' => true,
                'remote_route' => 'flower_api_clients_contact_findall_simple',
                'class' => '\Flower\ModelBundle\Entity\Clients\Contact',
                'text_property' => 'name',
                'minimum_input_length' => 2,
                'page_limit' => 10,
                'language' => 'es',
                'placeholder' => 'Seleccionar contactos',
            ])
            ->add('opportunity')
            ->add('account')
            ->add('project')
            ->add('users')
            ->add('startDate', 'collot_datetime', array('pickerOptions' =>
                array('format' => 'dd/mm/yyyy  hh:ii',
                    'autoclose' => true,
                    'todayBtn' => true,
                    'todayHighlight' => true,
                    'keyboardNavigation' => true,
                    'language' => 'en',
                )))
            ->add('endDate', 'collot_datetime', array('required' => false, 'pickerOptions' =>
                array('format' => 'dd/mm/yyyy  hh:ii',
                    'autoclose' => true,
                    'todayBtn' => true,
                    'todayHighlight' => true,
                    'keyboardNavigation' => true,
                    'language' => 'en',
                )))
            ->add('save', 'submit', array('label' => 'Save'));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Planner\Event',
            'translation_domain' => 'Event',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'event';
    }

}
