<?php

namespace App\Form;

use App\Entity\TaskList;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ContributorType extends AbstractType
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $taskList = $options['list'];
        if (!$taskList instanceof TaskList) {
            throw \RuntimeException('The contributor type requires an assocaited task list.');
        }

        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            throw \RuntimeException('The contributor type can only be used by logged in users.');
        }
        $user = $token->getUser();
        if (!$user instanceof User) {
            throw \RuntimeException('The contributor type can only be used by logged in users.');
        }

        $builder
            ->add('contributor', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $entityRepository) use ($taskList, $user) {
                    $existingContributors = [$user->getEmail(), $taskList->getOwner()->getEmail()];
                    foreach ($taskList->getContributors() as $contributor) {
                        $existingContributors[] = $contributor->getEmail();
                    }

                    $queryBuilder = $entityRepository->createQueryBuilder('contributor');

                    return $queryBuilder
                        ->where($queryBuilder->expr()->notIn('contributor.email', $existingContributors));
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('list');
    }
}
