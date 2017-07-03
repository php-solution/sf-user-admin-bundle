<?php

namespace PhpSolution\UserAdminBundle\Bridge;

use Doctrine\ORM\QueryBuilder;
use PhpSolution\UserAdminBundle\Entity\UserAdminInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class SonataUserAdmin
 */
class SonataUserAdmin extends AbstractAdmin
{
    /**
     * @param UserAdminInterface $userAdmin
     */
    public function prePersist($userAdmin)
    {
        $this->getConfigurationPool()->getContainer()->get('user_admin.reset_process')->encodePassword($userAdmin);
    }

    /**
     * @param UserAdminInterface $userAdmin
     */
    public function preUpdate($userAdmin)
    {
        $this->getConfigurationPool()->getContainer()->get('user_admin.reset_process')->encodePassword($userAdmin);
    }

    /**
     * @param string $context
     *
     * @return QueryBuilder
     */
    public function createQuery($context = 'list')
    {
        /* @var $query QueryBuilder */
        $query = parent::createQuery($context);
        /** @var UserAdminInterface $user */
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $query->andWhere(
            $query->expr()->neq($query->getRootAliases()[0] . '.id', ':user_id')
        );
        $query->setParameter('user_id', $user->getId());

        return $query;
    }

    /**
     * @return \Symfony\Component\Form\FormBuilder|\Symfony\Component\Form\FormBuilderInterface
     */
    public function getFormBuilder()
    {
        $this->formOptions['validation_groups'] = (!$this->getSubject() || is_null($this->getSubject()->getId()))
            ? 'registration'
            : 'edit_profile';

        return parent::getFormBuilder();
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('enabled', 'checkbox', ['required' => false])
            ->add('email', 'email', ['required' => true])
            ->add(
                'plainPassword',
                'repeated',
                [
                    'first_name' => 'password',
                    'second_name' => 'confirm',
                    'type' => 'password',
                    'invalid_message' => 'The password and confirm password fields must match.',
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Confirm Password'],
                    'required' => !$this->id($this->getSubject()),
                ]
            );
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('id')->add('email');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('email')
            ->add('date', 'date')
            ->add('enabled', 'boolean')
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                    ],
                ]
            );
    }
}