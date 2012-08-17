<?php

namespace GalleryBundle\Repository\Album;

use Doctrine\ORM\EntityRepository,
    GalleryBundle\Entity\Album\Album as AlbumEntity;

/**
 * Photo
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Photo extends EntityRepository
{
    public function findOneByAlbumAndFilePath(AlbumEntity $album, $filePath)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('GalleryBundle\Entity\Album\Photo', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.album', ':album'),
                    $query->expr()->eq('p.filePath', ':filePath')
                )
            )
            ->setParameter('album', $album->getId())
            ->setParameter('filePath', $filePath)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
