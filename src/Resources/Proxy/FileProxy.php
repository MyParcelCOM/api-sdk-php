<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use MyParcelCom\ApiSdk\Resources\File;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;
use Psr\Http\Message\StreamInterface;

/**
 * @method File getResource()
 */
class FileProxy implements FileInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_FILE;

    public function setDocumentType(string $documentType): self
    {
        $this->getResource()->setDocumentType($documentType);

        return $this;
    }

    public function getDocumentType(): string
    {
        return $this->getResource()->getDocumentType();
    }

    public function setFormats(array $formats): self
    {
        $this->getResource()->setFormats($formats);

        return $this;
    }

    public function addFormat(string $mimeType, string $extension): self
    {
        $this->getResource()->addFormat($mimeType, $extension);

        return $this;
    }

    public function getFormats(): array
    {
        return $this->getResource()->getFormats();
    }

    public function setStream(StreamInterface $stream, string $mimeType): self
    {
        $this->getResource()->setStream($stream, $mimeType);

        return $this;
    }

    public function getStream(?string $mimeType = null): ?StreamInterface
    {
        return $this->getResource()->getStream($mimeType);
    }

    public function setBase64DataFromResponseMeta(array $metaFiles): self
    {
        $this->getResource()->setBase64DataFromResponseMeta($metaFiles);

        return $this;
    }

    public function setBase64Data(string $data, string $mimeType): self
    {
        $this->getResource()->setBase64Data($data, $mimeType);

        return $this;
    }

    public function getBase64Data(?string $mimeType = null): ?string
    {
        return $this->getResource()->getBase64Data($mimeType);
    }

    public function setTemporaryFilePath(string $path, string $mimeType): self
    {
        $this->getResource()->setTemporaryFilePath($path, $mimeType);

        return $this;
    }

    public function getTemporaryFilePath(?string $mimeType = null): ?string
    {
        return $this->getResource()->getTemporaryFilePath($mimeType);
    }

    public function jsonSerialize(): array
    {
        $values = get_object_vars($this);
        unset($values['resource']);
        unset($values['api']);
        unset($values['uri']);

        return $this->arrayValuesToArray($values);
    }
}
