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
            ->add('description', null, array('required' => false))
            ->add('status', null, array('required' => false))
            ->add('visible', 'choice', array(
                'choices' => array('Private' => 0, 'Company' => 1, 'Public' => 2),
                'choices_as_values' => true,
            ))
            ->add('contacts')
            ->add('opportunity')
            ->add('account')
            ->add('project')
            ->add('users')
            ->add('startDate')
            ->add('endDate')
            ->add('save', 'submit', array('label' => 'Save'))
        ;
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
