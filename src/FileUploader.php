<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\UrlHelper;

class FileUploader
{
    private $uploadPath;
    private $slugger;
    private $urlHelper;
    private $relativeUploadsDir;

    public function __construct(string $uploadDirectory, string $publicPath, SluggerInterface $slugger, UrlHelper $urlHelper)
    {
        $this->uploadPath = $uploadDirectory;
        $this->slugger = $slugger;
        $this->urlHelper = $urlHelper;

        // Obtenez le répertoire de téléversement relatif au chemin public
        $this->relativeUploadsDir = str_replace($publicPath, '', $this->uploadPath) . '/';
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getUploadPath(), $fileName);
        } catch (FileException $e) {
            // Gérer l'exception si quelque chose se passe pendant le téléversement du fichier
            throw new FileException('Une erreur s\'est produite lors du téléversement du fichier.');
        }

        return $fileName;
    }

    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function getUrl(?string $fileName, bool $absolute = true)
    {
        if (empty($fileName)) {
            return null;
        }

        if ($absolute) {
            return $this->urlHelper->getAbsoluteUrl($this->relativeUploadsDir . $fileName);
        }

        return $this->urlHelper->getRelativePath($this->relativeUploadsDir . $fileName);
    }
}
