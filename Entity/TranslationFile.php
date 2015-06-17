<?php
/**
 * @author    Mike Lohmann <mike@protofy.com>
 * @copyright 2015 Protofy GmbH & CO. KG
 */
namespace Ibrows\SonataTranslationBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class TranslationFile
 */
class TranslationFile
{
    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * @var int
     */
    protected $id;

    /*
     * @var string
     */
    protected $filename;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // set the path property to the filename where you've saved the file
        $this->filename = $this->getFile()->getClientOriginalName();



        // clean up the file property as you won't need it anymore
        $this->setFile(null);
    }

    /**
     * Lifecycle callback to upload the file to the server
     */
    public function lifecycleFileUpload()
    {
        $this->upload();
    }
}