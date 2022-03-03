<?php

namespace App\Form\DataTransformer;

use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SiteTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (site) to a int
     *
     * @param  Site|null $site
     */
    public function transform($site): string
    {
        if (null === $site) {
            return '';
        }
        return $site->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $value
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($value): ?Site
    {
        if (!$value) {
            return null;
        }

        $site = $this->em->getRepository(Site::class)->find($value);

        if (is_null($site)) {
            throw new TransformationFailedException(sprintf(
                'Le site : "%s" n\'existe pas!',
                $value
            ));
        }

        return $site;
    }
}