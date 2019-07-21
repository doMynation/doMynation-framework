<?php namespace Domynation\Utils;

/**
 * Class Downloadify
 *
 * @package Domynation\Utils
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Downloadify
{

    /**
     * The path where files are uploaded.
     *
     * @var string
     */
    private $uploadPath;

    /**
     * Filters applied when uploading an image
     *
     * @var array
     */
    private $imageFilters;

    /**
     * Filters applied when uploading a PDF
     *
     * @var array
     */
    private $pdfFilters;

    /**
     * Downloadify constructor.
     *
     * @param string $uploadPath
     */
    public function __construct($uploadPath)
    {
        $this->uploadPath   = rtrim($uploadPath, '/');
        $this->imageFilters = [];
        $this->pdfFilters   = [];
    }

    /**
     * Adds an image processor.
     *
     * @param \Closure $closure
     */
    public function addImageFilter($closure)
    {
        $this->imageFilters[] = $closure;
    }

    /**
     * Adds a PDF processor.
     *
     * @param \Closure $closure
     */
    public function addPdfFilter($closure)
    {
        $this->pdfFilters[] = $closure;
    }

    /**
     * Performs the upload.
     *
     * @param $file
     *
     * @return array
     * @throws \Exception
     */
    public function run($file)
    {
        // Generate new name
        $ext         = $this->getFileExtension($file['name']);
        $newFileName = $this->generateNewFileName($ext);
        $newFilePath = $this->formatFilePath($newFileName);

        // Move the file
        $this->move($file['tmp_name'], $newFilePath);

        $fileData = [
            'name'         => $newFileName,
            'originalName' => $file['name'],
            'path'         => $newFilePath,
            'type'         => $file['type'],
            'size'         => $file['size'],
            'isImage'      => false
        ];

        // Run through each image filter
        if ($this->isImage($newFilePath)) {
            $fileData['isImage'] = true;

            foreach ($this->imageFilters as $closure) {
                $fileData = $closure($fileData);
            }
        }

        // Run through each PDF filter
        if ($this->isPdf($newFilePath)) {
            foreach ($this->pdfFilters as $closure) {
                $fileData = $closure($fileData);
            }
        }

        return $fileData;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    private function isPdf($file)
    {
        $ext             = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $imageExtensions = ['pdf'];

        return in_array($ext, $imageExtensions);
    }

    /**
     * @param $file
     *
     * @return bool
     */
    private function isImage($file)
    {
        $ext             = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'bmp', 'gif', 'png', 'tif'];

        return in_array($ext, $imageExtensions);
    }

    /**
     * Formats the full file path using the name, the extension
     * and the upload path
     *
     * @param string $name
     *
     * @return string
     */
    private function formatFilePath($name)
    {
        return $this->uploadPath . '/' . $name;
    }

    /**
     * Moves an uploaded file to the specified path
     *
     * @param array $file
     * @param $destination
     *
     * @return bool
     * @throws \Exception
     */
    private function move($file, $destination)
    {
        if (!@move_uploaded_file($file, $destination)) {
            throw new \Exception("Error moving file {$file} to {$this->uploadPath}");
        }
    }

    /**
     * Generates a unique name for a file
     *
     * @param string $ext
     *
     * @return string
     */
    private function generateNewFileName($ext)
    {
        return uniqid() . '.' . $ext;
    }

    /**
     * Extracts the extension portion of a file name
     *
     * @param array $file
     *
     * @return string
     */
    private function getFileExtension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }
}