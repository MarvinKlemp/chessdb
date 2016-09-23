<?php


namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DropboxInfoRepository extends EntityRepository
{
    public function findOrCreateOneByUser(User $user)
    {
        $dropboxInfo = $this
            ->createQueryBuilder('di')
            ->where('di.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$dropboxInfo) {
            $dropboxInfo = new DropboxInfo();
            $dropboxInfo->setUser($user);
        }

        return $dropboxInfo;
    }

    public function save(DropboxInfo $dropboxInfo, $flush = true)
    {
        $this->getEntityManager()->persist($dropboxInfo);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}