<?php

namespace Remyb98\DoctrineFieldEncrypt\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Remyb98\DoctrineFieldEncrypt\Attribute\Encrypt;
use Remyb98\DoctrineFieldEncrypt\Service\EncryptionService;
use Remyb98\DoctrineFieldEncrypt\Interface\EncryptableEntity;

final readonly class EncryptSubscriber implements EventSubscriber
{

    public function __construct(private EncryptionService $encryptionService)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postLoad,
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof EncryptableEntity) {
            $this->encryptField($object);
        }
    }

    private function encryptField(EncryptableEntity $entity): void
    {
        $reflectionClass = new \ReflectionClass($entity);
        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes(Encrypt::class);
            if (!empty($attributes)) {
                $property->setAccessible(true);
                $clearValue = $property->getValue($entity);
                $encryptedValue = $this->encryptionService->encrypt($clearValue);
                $property->setValue($entity, $encryptedValue);
            }
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof EncryptableEntity) {
            $this->encryptField($object);
        }
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $object = $args->getObject();
        if ($object instanceof EncryptableEntity) {
            $this->decryptField($object);
        }
    }

    private function decryptField(EncryptableEntity $entity): void
    {
        $reflectionClass = new \ReflectionClass($entity);
        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes(Encrypt::class);
            if (!empty($attributes)) {
                $property->setAccessible(true);
                $encryptedValue = $property->getValue($entity);
                $clearValue = $this->encryptionService->decrypt($encryptedValue);
                $property->setValue($entity, $clearValue);
            }
        }
    }
}
