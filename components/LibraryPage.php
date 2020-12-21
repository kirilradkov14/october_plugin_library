<?php

namespace Pensoft\Library\Components;

use \Cms\Classes\ComponentBase;
use Pensoft\Library\Classes\ZipFiles;
use Pensoft\Library\Models\Library;

class LibraryPage extends ComponentBase
{
    public function onRun()
    {
        $this->addJs('assets/js/def.js');
        $this->prepareVars();
    }

    public function componentDetails()
    {
        return [
            'name' => 'LibraryPage',
            'description' => 'Displays a collection of libraries.'
        ];
    }

    public function prepareVars()
    {
        $options = post('Filter', []);
        $library = Library::isVisible()->listFrontEnd($options);
        $this->page['records'] = $library->get();
        $this->page['sortOptions'] = Library::$allowSortingOptions;
        $this->page['sortTypesOptions'] = Library::$allowSortTypesOptions;
        $this->page['total_file_size_bites'] = $this->page['records']->reduce(function ($carry, $item) {
            if ($item->file) {
                return $carry + $item->file->file_size;
            }
            return $carry;
        }, 0);
        $this->page['total_file_size'] = $this->page['total_file_size_bites'];
    }

    public function onFilterRecords()
    {
        $this->prepareVars();
    }

    public function onDownloadAll()
    {
        $zip = (new ZipFiles(new Library))->downloadFiles();
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=publications.zip");
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile($zip);
        exit;
    }
}